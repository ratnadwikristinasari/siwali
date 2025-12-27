<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
class MahasiswaHelper {
public static function getMahasiswa(string $token, ?string $majorId = null, ?int $page = 1,): array
  {
    $queryParams = [
      'major_id' => $majorId,
      'page' => $page
    ];
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])
      ->withQueryParameters($queryParams)
      ->get(config('app.super_app_url') . '/students');

    return $response->json();
  }
}
