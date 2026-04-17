<?php

namespace App\Helpers;

class SessionApiHelper
{
    public static function getAsOptions(string $token)
    {
        $response = GlobalHelper::requestWithToken(
            '/sessions/options',
            $token,
            'GET'
        );

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
