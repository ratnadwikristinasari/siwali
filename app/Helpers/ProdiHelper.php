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
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->withQueryParameters($queryParams)
            ->get(config('app.super_app_url_internal') . '/study-programs');

        return $response->json();
    }
}
