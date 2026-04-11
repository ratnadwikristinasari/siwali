<?php

namespace App\Http\Controllers\page;


use App\Helpers\AuthHelper;
use App\Helpers\CheckJtiformStatusHelper;
use App\Helpers\DosenHelper;
use App\Helpers\ESignApiHelper;
use App\Helpers\FileHelper;
use App\Helpers\MahasiswaHelper;
use App\Helpers\MajorHelper;
use App\Helpers\SemesterApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Advise;
use App\Models\User;
use App\Services\NotificationPublisher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CPerwalian extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:gpa_advising,non_gpa_advising',
            'keluhan' => 'required|string',
        ]);

        $studentUser = Auth::user();
        $dataAuth = AuthHelper::getauth('', $studentUser->token);

        if ($request->type === 'gpa_advising') {
            $studentSemester = collect($dataAuth['data']['student_detail']['student_semester'] ?? [])
                ->firstWhere('semester_id', $request->semester_id);

            $isEven = (int) $studentSemester['semester'] % 2 === 0;

            $checkStatusJtiform = CheckJtiformStatusHelper::check(
                $studentSemester['session_id'] ?? '',
                $isEven,
                $studentUser->external_id
            );

            if ($checkStatusJtiform === false && $request->type === 'gpa_advising') {
                return back()->withErrors('Anda belum mengisi form Evaluasi Dosen untuk semester ini. Silakan isi form terlebih dahulu sebelum mengajukan perwalian.');
            } elseif ($checkStatusJtiform === null && $request->type === 'gpa_advising') {
                return back()->withErrors('Gagal memeriksa status JTIFORM. Silakan coba lagi nanti.');
            }
        }

        $advisor = collect($dataAuth['data']['student_detail']['supervisor_lectures'])->firstWhere('position', 'ACADEMIC_ADVISOR');
        if (!$advisor) {
            return back()->withErrors('Dosen Wali tidak ditemukan');
        }

        $employeeId = $advisor['employee_id'];
        $datadosen = DosenHelper::getdosenById($studentUser->token, $employeeId);

        $lectureUser = User::updateOrCreate(
            ['external_id' => $datadosen['data']['user']['id']],
            [
                'name' => $datadosen['data']['user']['name'],
                'email' => $datadosen['data']['user']['email']
            ]
        );
        if (!$lectureUser) {
            return back()->withErrors([
                'advisor' => 'Dosen Wali tidak ditemukan'
            ]);
        }

        $wali = Advise::create([
            'student_user_id' => $studentUser->id,
            'lecture_user_id' => $lectureUser->id,
            'student_id' => $dataAuth['data']['student_detail']['id'],
            'lecture_id' => $employeeId,
            'status' => empty($request->masukan) ? 'Pending' : 'Done',
            'semester' => $request->semester ?? null,
            'ipk' => $request->ipk,
            'keluhan' => $request->keluhan,
            'semester_id' => $request->semester_id ?? null,
            'type' => $request->type,
        ]);

        $wali->load('student', 'lecture');

        $publisher = app(NotificationPublisher::class);

        $publisher->send([
            'app_env' => config('app.env') == 'production' ? 'production' : 'dev',
            'event' => 'advise-submission-to-advisor',
            'recipient' => [
                'email' => strtolower($lectureUser->email),
            ],
            'channels' => ['email'],
            'subject' => 'Pengajuan Perwalian Mahasiswa',
            'data' => [
                'name' => $lectureUser->name,
                'studentName' => $wali->student->name ?? '',
                'gpa' => $wali->ipk ?? '',
                'complaint' => $wali->keluhan ?? '',
                'form_url' => route('perwalian.detail', ['id' => $wali->id]),
            ],
        ]);

        return redirect()
            ->route('dataperwalian')
            ->with('Success', 'Perwalian Berhasil Diajukan');
    }

    public function edit($id)
    {
        $perwalian = Advise::findOrFail($id);

        $eSign = ESignApiHelper::signDocument(
            Auth::user()->token,
            'Perwalian Approval',
            Auth::user()->name ?? 'N/A'
        );

        session(['esign_base64' => $eSign['data']['qr_code_base64']]);

        $fullUrlFile = FileHelper::get('khs_files', $perwalian->khs);

        return view('content.editform-perwalian', compact('perwalian', 'eSign', 'fullUrlFile'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'masukan' => 'required|string',
        ]);

        $perwalian = Advise::findOrFail($id);

        if ($perwalian->type === 'non_gpa_advising') {
            $perwalian->update([
                'status' => 'done',
                'masukan' => $request->masukan,
            ]);

            return redirect()
                ->route('dataperwaliandosen')
                ->with('success', 'Perwalian Selesai!');
        }

        $token = Auth::user()->token;
        $authData = AuthHelper::getauth(null, $token)['data'] ?? [];

        $studentScore = MahasiswaHelper::getStudentScore(
            $token,
            $perwalian->student_id ?? '',
            $perwalian->semester_id
        );

        $majorHeadData = MajorHelper::getById(
            $token,
            $perwalian->lecture->major_id ?? ''
        );

        $academicYear = SemesterApiHelper::getById(
            $token,
            $perwalian->semester_id ?? ''
        );

        $data = $studentScore['data'] ?? [];

        $filename = 'khs_' . $perwalian->student->external_id . '_' . $perwalian->semester . '_' . time() . '.pdf';

        $eSign = ESignApiHelper::signDocument(
            $token,
            $filename,
            $authData['name'] ?? 'N/A'
        );

        $lectureName = $authData['name'] ?? 'N/A';
        $lecturerNip = $authData['employee_detail']['nip'] ?? 'N/A';

        $pdf = Pdf::loadView('content.khs.khs-pdf', compact(
            'data',
            'academicYear',
            'majorHeadData',
            'lectureName',
            'lecturerNip',
            'eSign',
        ))->setPaper('a4', 'portrait');
        $pdfContent = $pdf->output();

        if (isset($eSign['data']['id'])) {
            ESignApiHelper::updateDocumentHash(
                $token,
                $eSign['data']['id'],
                hash('sha256', $pdfContent)
            );
        }

        $signedPdfContent = ESignApiHelper::signPDF(
            $token,
            $pdfContent,
            $authData['name'] ?? 'N/A',
            'Dosen Wali'
        );

        Storage::disk('s3')->put('khs_files/' . $filename, $signedPdfContent);

        $perwalian->update([
            'status' => 'signed',
            'masukan' => $request->masukan,
            'khs' => $filename
        ]);

        $perwalian->load('student');

        $publisher = app(NotificationPublisher::class);
        $publisher->send([
            'app_env' => config('app.env') == 'production' ? 'production' : 'dev',
            'event' => 'advise-approved-by-advisor-for-student',
            'recipient' => [
                'email' => strtolower($perwalian->student->email),
            ],
            'channels' => ['email'],
            'subject' => 'Perwalian Disetujui - Menunggu Tanda Tangan Kajur',
            'data' => [
                'name' => $perwalian->student->name ?? '',
                'advisor_note' => $request->masukan ?? '',
                'form_url' => route('dataperwalian'),
            ],
        ]);

        if (!empty($majorHeadData['data']['head']['email'])) {
            $publisher->send([
                'app_env' => config('app.env') == 'production' ? 'production' : 'dev',
                'event' => 'advise-approved-by-advisor-for-kajur',
                'recipient' => [
                    'email' => strtolower($majorHeadData['data']['head']['email'] ?? ''),
                ],
                'channels' => ['email'],
                'subject' => 'Perwalian Disetujui - Menunggu Tanda Tangan Kajur',
                'data' => [
                    'name' => $majorHeadData['data']['head']['name'] ?? '',
                    'student_name' => $perwalian->student->name ?? '',
                    'student_nim' => explode('@', $perwalian->student->email)[0] ?? '',
                    'advisor_name' => $authData['name'] ?? '',
                    'form_url' => route('page.need_sign'),
                ],
            ]);
        }

        return redirect()
            ->route('dataperwaliandosen')
            ->with('success', 'Perwalian Selesai!');
    }
}
