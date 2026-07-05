<?php

namespace App\Http\Controllers\page;

use App\Helpers\MahasiswaHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuthHelper;
use App\Helpers\DosenHelper;
use App\Helpers\MajorHelper;
use App\Helpers\SemesterApiHelper;
use Barryvdh\DomPDF\Facade\Pdf;


class CMahasiswa extends Controller
{
    public function index(Request $request)
    {

        $token = Auth::user()->token;
        $majorId = Auth::user()->major_id;
        $page = $request->query('page', 1);
        $search = $request->query('search', '');

        $response = MahasiswaHelper::getMahasiswa($token, $majorId, $page, $search);

        if (!isset($response['data']) || empty($response['data'])) {
            $data = collect();
            $meta = ['total' => 0, 'per_page' => 10, 'page' => 1];
        } else {
            $data = collect($response['data']);
            $meta = $response['meta'] ?? ['total' => count($data), 'per_page' => 10, 'page' => $page];
        }

        if ($data->isNotEmpty()) {
            $studentIds = $data->pluck('student_id')->toArray();
            $semesterIds = $data
                ->map(function ($mhs) {
                    return collect($mhs['student_semesters'] ?? [])
                        ->firstWhere('is_active', true)['semester_id'] ?? null;
                })
                ->filter()
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

            $data = $data->map(function ($mhs) use ($advises) {
                $activeSemesterId = collect($mhs['student_semesters'] ?? [])
                    ->firstWhere('is_active', true)['semester_id'] ?? null;

                $lookupKey = $mhs['student_id'] . '|' . $activeSemesterId;

                $mhs['status_perwalian'] = $activeSemesterId
                    ? ($advises[$lookupKey] ?? null)
                    : null;
                return $mhs;
            });
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

        return view('content.datamahasiswa', compact('mahasiswas'));
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
