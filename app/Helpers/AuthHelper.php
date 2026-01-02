<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class AuthHelper {
    public static function getauth(string $majorId, string $token, ?int $page = 1,) {
        $queryParams = [
      'major_id' => $majorId,
      'page' => $page
    ];
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])
      ->withQueryParameters($queryParams)
      ->get(config('app.super_app_url') . '/auth/me');
    return $response->json();

    }
}