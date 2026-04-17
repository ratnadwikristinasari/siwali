<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class DosenHelper
{
    public static function getdosen(
        string $token,
        ?string $majorId = null,
        ?int $page = 1,
        ?string $search = null,
        ?string $studyProgramId = null
    ): array {
        $queryParams = [
            'major_id' => $majorId,
            'page' => $page,
            'search' => $search,
            'position' => 'DOSEN',
            'study_program_id' => $studyProgramId,
        ];
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->withQueryParameters($queryParams)
            ->get(config('app.super_app_url_internal') . '/employees');
        return $response->json();
    }

    public static function getdosenById(string $token, string $employeeId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->get(config('app.super_app_url_internal') . '/employees/' . $employeeId);
        return $response->json();
    }

    public static function getLectureByUserId(string $token, string $userId)
    {
        $response = GlobalHelper::requestWithToken(
            '/employees/user/' . $userId,
            $token,
            'GET'
        );

        return $response->json()['data'] ?? null;
    }
}
