<?php

namespace App\Http\Controllers\page;

use App\Helpers\AuthHelper;
use App\Helpers\DosenHelper;
use App\Helpers\MahasiswaHelper;
use App\Helpers\MajorHelper;
use App\Helpers\SemesterApiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class Cbiodata extends Controller
{
    public function biodata()
    {

        $user = Auth::user();
        $userToken = $user->token;
        $userData = AuthHelper::getauth(
            null,
            $userToken
        );

        $id = $user->student_employee_id;

        if ($user->hasRole('orang_tua')) {
            $raw = MahasiswaHelper::getStudentDetails($userToken, $user->studentParents->first()->student->student_employee_id);
            $rawData = $raw['data'];
            $semesters = collect($rawData['semesters'] ?? []);
            $lastSemester = $semesters->sortByDesc(fn($s) => $s['year'] * 10 + $s['semester'])->first();

            $userData = [
                'message' => $raw['message'],
                'data' => [
                    'name'          => $rawData['user']['name'],
                    'email'         => $rawData['user']['email'],
                    'status'        => $rawData['user']['status'],
                    'gender'        => $rawData['user']['gender'],
                    'religion'      => $rawData['user']['religion'],
                    'birth_place'   => $rawData['user']['birth_place'],
                    'birth_date'    => $rawData['user']['birth_date'],
                    'nationality'   => $rawData['user']['nationality'],
                    'phone_number'  => $rawData['user']['phone_number'],
                    'address'       => $rawData['user']['address'],
                    'student_detail' => [
                        'nim'               => $rawData['nim'],
                        'generation'        => $rawData['generation'],
                        'study_program_name' => $rawData['study_program_name'],
                        'major_name'        => $rawData['major_name'],
                        'supervisor_lectures' => $rawData['supervisor_lectures'] ?? [],
                        'student_semester'  => $semesters->map(fn($s) => array_merge($s, [
                            'is_active' => $lastSemester && $s['id'] === $lastSemester['id'],
                        ]))->values()->toArray(),
                    ],
                ],
            ];

            $id = $user->studentParents->first()->student->student_employee_id;
        }

        $gpa = MahasiswaHelper::getStudentGpa($userToken, $id);

        return view('content.biodata', compact('userData', 'id', 'gpa'));
    }

    public function previewGPA(string $studentId, string $semesterId)
    {
        $user = Auth::user();
        $token = Auth::user()->token;
        $authData = AuthHelper::getauth(null, $token)['data'] ?? [];

        if ($user->hasRole('orang_tua')) {
            $raw = MahasiswaHelper::getStudentDetails($token, $user->studentParents->first()->student->student_employee_id);
            $rawData = $raw['data'];
            $semesters = collect($rawData['semesters'] ?? []);
            $lastSemester = $semesters->sortByDesc(fn($s) => $s['year'] * 10 + $s['semester'])->first();

            $authData = [
                'name'          => $rawData['user']['name'],
                'email'         => $rawData['user']['email'],
                'status'        => $rawData['user']['status'],
                'gender'        => $rawData['user']['gender'],
                'religion'      => $rawData['user']['religion'],
                'birth_place'   => $rawData['user']['birth_place'],
                'birth_date'    => $rawData['user']['birth_date'],
                'nationality'   => $rawData['user']['nationality'],
                'phone_number'  => $rawData['user']['phone_number'],
                'address'       => $rawData['user']['address'],
                'student_detail' => [
                    'nim'               => $rawData['nim'],
                    'generation'        => $rawData['generation'],
                    'study_program_name' => $rawData['study_program_name'],
                    'major_name'        => $rawData['major_name'],
                    'm_major_id' => $rawData['major_id'],
                    'supervisor_lectures' => $rawData['supervisor_lectures'] ?? [],
                    'student_semester'  => $semesters->map(fn($s) => array_merge($s, [
                        'is_active' => $lastSemester && $s['id'] === $lastSemester['id'],
                    ]))->values()->toArray(),
                ],
            ];
        }

        $supervisorLecture = collect($authData['student_detail']['supervisor_lectures'] ?? [])
            ->firstWhere('position', 'ACADEMIC_ADVISOR');

        $studentScore = MahasiswaHelper::getStudentScore(
            $token,
            $studentId,
            $semesterId
        );

        $majorHeadData = MajorHelper::getById(
            $token,
            $authData['student_detail']['m_major_id'] ?? ''
        );

        $academicYear = SemesterApiHelper::getById(
            $token,
            $semesterId ?? ''
        );

        $supervisorData = DosenHelper::getLectureByUserId(
            $token,
            $supervisorLecture['user_id'] ?? ''
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
            // 'authData',
            'lectureName',
            'lecturerNip'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('khs.pdf');
    }
}
