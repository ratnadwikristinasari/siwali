<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SessionApiHelper
{
    public static function getAsOptions(string $token)
    {
        $response = Http::WithoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->get(config('app.super_app_url') . '/sessions/options');

        return $response->json()['data'] ?? null;
    }

    public static function getById(string $token, string $id)
    {
        $response = GlobalHelper::requestWithToken(
            '/sessions/' . $id,
            $token,
            'GET'
        );

        return $response->json()['data'] ?? null;
    }
}
