<?php

namespace App\Http\Controllers\page;

use App\Helpers\AuthHelper;
use App\Helpers\MahasiswaHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Cbiodata extends Controller
{
    public function biodata(Request $request)
    {

        $user = Auth::user();
        $userToken = $user->token;
        $userData = AuthHelper::getauth(
            null,
            $userToken
        );

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
                    'phone_number'  => null,
                    'address'       => null,
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
        }

        return view('content.biodata', compact('userData'));
    }
}
