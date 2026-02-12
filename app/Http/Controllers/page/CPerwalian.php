<?php

namespace App\Http\Controllers\page;


use App\Helpers\AuthHelper;
use App\Helpers\DosenHelper;
use App\Helpers\ESignApiHelper;
use App\Helpers\FileHelper;
use App\Helpers\MahasiswaHelper;
use App\Helpers\MajorHelper;
use App\Helpers\SemesterApiHelper;
use App\Http\Controllers\Controller;
use App\Mail\AjukanPerwalianMail;
use App\Mail\PengajuanDiterimaMail;
use App\Models\Advise;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

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

        $status = empty($request->masukan) ? 'Pending' : 'Done';

        $activeSemester = array_values(array_filter(
            $dataAuth['data']['student_detail']['student_semester'],
            fn($s) => $s['is_active'] === true
        ));

        $semesterId = $activeSemester[0]['semester_id'] ?? null;
        $semesterAktif = $activeSemester[0]['semester'] ?? null;

        if (!$semesterAktif) {
            return back()->withErrors('Semester aktif tidak ditemukan');
        }

        $wali = Advise::create([
            'student_user_id' => $studentUser->id,
            'lecture_user_id' => $lectureUser->id,
            'student_id' => $dataAuth['data']['student_detail']['id'],
            'lecture_id' => $employeeId,
            'status' => empty($request->masukan) ? 'Pending' : 'Done',
            'semester' => $semesterAktif,
            'ipk' => $request->ipk,
            'keluhan' => $request->keluhan,
            'semester_id' => $semesterId,
            'type' => $request->type,
        ]);

        $wali->load('student', 'lecture');

        if ($status === 'Pending') {
            Mail::to($lectureUser->email)
                ->queue(new AjukanPerwalianMail($wali));
        }

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

        // $perwalian->load('student', 'lecture');
        // Mail::to($perwalian->student->email)
        //     ->queue(new PengajuanDiterimaMail($perwalian));

        return redirect()
            ->route('dataperwaliandosen')
            ->with('success', 'Perwalian Selesai!');
    }
}
