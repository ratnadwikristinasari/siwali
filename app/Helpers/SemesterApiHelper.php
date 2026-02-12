<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SemesterApiHelper
{
    public static function getSemesterAsOption(string $token, ?string $sessionID = null): ?array
    {
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->withQueryParameters([
                'session_id' => $sessionID,
            ])
            ->get(config('app.super_app_url') . '/semesters/options');

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
