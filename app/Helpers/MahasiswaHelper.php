<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class MahasiswaHelper
{
    public static function getMahasiswa(string $token, ?string $majorId = null, ?int $page = 1, ?string $search = ''): array
    {
        $queryParams = [
            'search' => $search,
            'major_id' => $majorId,
            'page' => $page
        ];
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->withQueryParameters($queryParams)
            ->get(config('app.super_app_url') . '/supervisor-lectures/by-lecture');

        $response = GlobalHelper::requestWithToken(
            '/supervisor-lectures/by-lecture',
            $token,
            'GET',
            [],
            $queryParams
        );

        return $response->json();
    }

    public static function getAllStudents(
        string $token,
        array $queryParams = []
    ): array {
        $response = GlobalHelper::requestWithToken(
            '/students',
            $token,
            'GET',
            [],
            $queryParams
        );

        return $response->json();
    }

    public static function getStudentScore(string $token, string $studentId, ?string $semesterId = null, ?int $page = 1): array
    {
        $queryParams = [
            'page' => $page
        ];

        $response = GlobalHelper::requestWithToken(
            '/grades/history',
            $token,
            'GET',
            [
                'student_id' => $studentId,
                'semester_id' => $semesterId
            ],
            $queryParams
        );

        return $response->json();
    }

    public static function calculateGPA(array $grades): float
    {
        $gradeMapping = [
            'A'  => 4.0,
            'AB' => 3.5,
            'B'  => 3.0,
            'BC' => 2.5,
            'C'  => 2.0,
            'D'  => 1.0,
            'E'  => 0.0,
        ];

        $totalCredits = 0;
        $totalMutu = 0.0;

        foreach ($grades as $grade) {

            $letter = strtoupper($grade['grade_letter'] ?? '');
            $credit = (int) ($grade['credit'] ?? 0);

            if (!isset($gradeMapping[$letter]) || $credit <= 0) {
                continue;
            }

            $bobot = $gradeMapping[$letter];
            $mutu = $bobot * $credit;

            $totalMutu += $mutu;
            $totalCredits += $credit;
        }

        if ($totalCredits === 0) {
            return 0.0;
        }

        return round($totalMutu / $totalCredits, 2);
    }
}
