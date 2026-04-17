<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class ProdiHelper
{
    public static function getprodi(string $token, ?string $majorId = null, ?int $page = 1, ?string $search = null): array
    {
        $queryParams = [
            'major_id' => $majorId,
            'page' => $page,
            'search' => $search
        ];

        $response = GlobalHelper::requestWithToken(
            '/study-programs',
            $token,
            'GET',
            $queryParams
        );

        return $response->json();
    }
}
