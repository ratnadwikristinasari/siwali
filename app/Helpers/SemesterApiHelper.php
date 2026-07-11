<?php

namespace App\Helpers;

class SemesterApiHelper
{
    public static function getSemesterAsOption(string $token, ?string $sessionID = null): ?array
    {
        $response = GlobalHelper::requestWithToken(
            '/semesters/options',
            $token,
            'GET',
            [],
            [
                'session_id' => $sessionID,
            ]
        );

        return $response->json()['data'] ?? null;
    }

    public static function getById(string $token, string $id): ?array
    {
        $response = GlobalHelper::requestWithToken(
            '/semesters/' . $id,
            $token,
            'GET'
        );

        return $response->json()['data'] ?? null;
    }
}
