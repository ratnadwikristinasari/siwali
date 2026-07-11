<?php

namespace App\Helpers;

class AuthHelper
{
    public static function getauth(?string $majorId, string $token, ?int $page = 1,)
    {
        $queryParams = [
            'major_id' => $majorId,
            'page' => $page
        ];
        $response = GlobalHelper::requestWithToken(
            '/auth/me',
            $token,
            'GET',
            [],
            $queryParams
        );

        return $response->json();
    }
}
