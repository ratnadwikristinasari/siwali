<?php

namespace App\Http\Controllers\page;

use App\Helpers\MahasiswaHelper;
use App\Http\Controllers\Controller;
use App\Services\NotificationPublisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuthHelper;
use App\Helpers\DosenHelper;
use App\Helpers\MajorHelper;
use App\Helpers\SemesterApiHelper;
use App\Helpers\SessionApiHelper;
use Barryvdh\DomPDF\Facade\Pdf;


class CMahasiswa extends Controller
{
    public function index(Request $request)
    {

        $token = Auth::user()->token;
        $majorId = Auth::user()->major_id;
        $page = (int) $request->query('page', 1);
        $search = $request->query('search', '');
        $selectedSessionId = $request->query('session_id', '');
        $selectedSemesterId = $request->query('semester_id', $request->query('semester', ''));
        $statusAkademikFilter = $request->query('status_akademik', '');
        $statusPerwalianFilter = strtolower($request->query('status_perwalian', ''));

    

        $response = MahasiswaHelper::getMahasiswa(
            $token,
            $majorId,
            $page,
            $search,
            $selectedSessionId,
            $selectedSemesterId,
        );

        if (!isset($response['data']) || empty($response['data'])) {
            $data = collect();
            $meta = ['total' => 0, 'per_page' => 10, 'page' => 1];
        } else {
            $data = collect($response['data']);
            $meta = $response['meta'] ?? ['total' => count($data), 'per_page' => 10, 'page' => $page];
        }

        if ($data->isNotEmpty()) {
            $studentIds = $data->pluck('student_id')->toArray();
            $studentSemesterMap = $data->mapWithKeys(function ($mhs) use ($selectedSemesterId, $selectedSessionId) {
                $studentSemesters = collect($mhs['student_semesters'] ?? []);

                if (!empty($selectedSemesterId)) {
                    return [$mhs['student_id'] => $selectedSemesterId];
                }

                if (!empty($selectedSessionId)) {
                    $sessionSemester = $studentSemesters->first(function ($semester) use ($selectedSessionId) {
                        return (string) ($semester['session_id'] ?? '') === (string) $selectedSessionId
                            && ($semester['is_active'] ?? false);
                    });

                    if (empty($sessionSemester)) {
                        $sessionSemester = $studentSemesters->first(function ($semester) use ($selectedSessionId) {
                            return (string) ($semester['session_id'] ?? '') === (string) $selectedSessionId;
                        });
                    }

                    return [$mhs['student_id'] => $sessionSemester['semester_id'] ?? null];
                }

                return [
                    $mhs['student_id'] => $studentSemesters->firstWhere('is_active', true)['semester_id'] ?? null,
                ];
            });

            $semesterIds = $studentSemesterMap
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $advises = collect();

            if (!empty($studentIds) && !empty($semesterIds)) {
                $studentPlaceholders = implode(',', array_fill(0, count($studentIds), '?'));
                $semesterPlaceholders = implode(',', array_fill(0, count($semesterIds), '?'));

                $rows = DB::select(
                    "select student_id, semester_id, status, created_at
                    from advise
                    where type = ?
                    and student_id in ($studentPlaceholders)
                    and semester_id in ($semesterPlaceholders)
                    order by created_at desc",
                    array_merge(['gpa_advising'], $studentIds, $semesterIds)
                );

                $advises = collect($rows)
                    ->groupBy(function ($advise) {
                        return $advise->student_id . '|' . $advise->semester_id;
                    })
                    ->map(function ($items) {
                        $first = $items->first();
                        return $first ? strtolower($first->status) : null;
                    });
            }

            $data = $data->map(function ($mhs) use ($advises, $studentSemesterMap) {
                $activeSemesterId = $studentSemesterMap[$mhs['student_id']] ?? null;

                $lookupKey = $mhs['student_id'] . '|' . $activeSemesterId;
                $reminderKey = $activeSemesterId
                    ? 'student-reminder:' . $mhs['student_id'] . ':' . $activeSemesterId
                    : null;

                $statusPerwalian = $activeSemesterId
                    ? ($advises[$lookupKey] ?? null)
                    : null;

                $mhs['active_semester_id'] = $activeSemesterId;
                $mhs['status_perwalian'] = $statusPerwalian;
                $mhs['can_send_reminder'] = $activeSemesterId
                    && $statusPerwalian === null
                    && !Cache::has($reminderKey);
                return $mhs;
            });

            if ($statusAkademikFilter !== '') {
                $data = $data->filter(function ($mhs) use ($statusAkademikFilter) {
                    if ($statusAkademikFilter === 'TANPA_KETERANGAN') {
                        return ($mhs ['status'] ?? null) === null || ($mhs['status'] ?? '') === 'TANPA KETERANGAN';
                    }
                    return ($mhs['status'] ?? '') === $statusAkademikFilter;
                })->values();
            }

            if ($statusPerwalianFilter !== '') {
                $data = $data->filter(function ($mhs) use ($statusPerwalianFilter) {
                    $statusPerwalian = strtolower($mhs['status_perwalian'] ?? '');

                    if ($statusPerwalianFilter === 'belum') {
                        return $statusPerwalian === '';
                    }

                    return $statusPerwalian === $statusPerwalianFilter;
                })->values();
            }
        }

        if ($statusPerwalianFilter !== '') {
            $meta['total'] = count($data);
        }

        $mahasiswas = new \Illuminate\Pagination\LengthAwarePaginator(
            $data,
            $meta['total'] ?? 0,
            $meta['per_page'] ?? 10,
            $meta['page'] ?? 1,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $sessions = SessionApiHelper::getAsOptions($token);

        return view('content.datamahasiswa', compact('mahasiswas', 'sessions'));
    }

    public function sendReminder(Request $request, string $studentId, string $semesterId)
    {
        $token = Auth::user()->token;
        $cacheKey = 'student-reminder:' . $studentId . ':' . $semesterId;

        if (!Cache::add($cacheKey, true, now()->addDay())) {
            return redirect()
                ->route('datamahasiswa')
                ->with('error', 'Pengingat untuk mahasiswa ini sudah dikirim hari ini.');
        }

        try {
            $studentDetails = MahasiswaHelper::getStudentDetails($token, $studentId);
            $studentData = $studentDetails['data']['user'] ?? [];

            $email = $studentData['email'] ?? null;

            if (empty($email)) {
                Cache::forget($cacheKey);

                return redirect()
                    ->route('datamahasiswa')
                    ->with('error', 'Email mahasiswa tidak ditemukan.');
            }

            $publisher = app(NotificationPublisher::class);
            $publisher->send([
                'app_env' => config('app.env') == 'production' ? 'production' : 'dev',
                'event' => 'advise-reminder-for-student',
                'recipient' => ['email' => strtolower($email)],
                'channels' => ['email'],
                'subject' => 'Pengingat Perwalian - Mohon Segera Melakukan Perwalian',
                'data' => [
                    'name' => $studentData['name'] ?? '',
                    'student_nim' => $studentDetails['data']['nim'] ?? '',
                    'advisor_name' => Auth::user()->name ?? '',
                    'form_url' => route('form-perwalian'),
                ],
            ]);
        } catch (\Throwable $throwable) {
            Cache::forget($cacheKey);

            return redirect()
                ->route('datamahasiswa')
                ->with('error', 'Gagal mengirim pengingat. Silakan coba lagi.');
        }

        return redirect()
            ->route('datamahasiswa')
            ->with('success', 'Pengingat perwalian berhasil dikirim.');
    }

    public function previewGPA($studentId, $semesterId)
    {
        $token = Auth::user()->token;
        $authData = AuthHelper::getauth(null, $token)['data'] ?? [];

        $studentScore = MahasiswaHelper::getStudentScore(
            $token,
            $studentId,
            $semesterId
        );

        $majorHeadData = MajorHelper::getById(
            $token,
            Auth::user()->major_id ?? ''
        );

        $academicYear = SemesterApiHelper::getById(
            $token,
            $semesterId ?? ''
        );

        $supervisorData = DosenHelper::getLectureByUserId(
            $token,
            Auth::user()->external_id ?? ''
        );

        $lectureName = $supervisorData['user']['name'] ?? 'N/A';
        $lecturerNip = $supervisorData['nip'] ?? 'N/A';

        $data = $studentScore['data'] ?? [];

        if (empty($data)) {
            return redirect()->back()->with('error', 'Data KHS tidak ditemukan untuk mahasiswa ini pada semester yang dipilih.');
        }

        $pdf = Pdf::loadView('content.khs.khs-pdf', compact(
            'data',
            'academicYear',
            'majorHeadData',
            'authData',
            'lectureName',
            'lecturerNip'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('khs.pdf');
    }
}
