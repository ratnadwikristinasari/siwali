<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class MajorHelper
{
    public static function getmajor(string $majorId, string $token, ?int $page = 1,)
    {
        $queryParams = [
            'major_id' => $majorId,
            'page' => $page
        ];
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->withQueryParameters($queryParams)
            ->get(config('app.super_app_url') . '/majors');
        return $response->json();
    }

    public static function getById(string $token, string $majorId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->get(config('app.super_app_url_internal') . '/majors/' . $majorId);

        return $response->json();
    }
}
