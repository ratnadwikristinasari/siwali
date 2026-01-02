<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
class DosenHelper {
public static function getdosen(string $token, ?string $majorId = null, ?int $page = 1,): array
  {
    $queryParams = [
      'major_id' => $majorId,
      'page' => $page
    ];
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])
      ->withQueryParameters($queryParams)
      ->get(config('app.super_app_url') . '/employees');
      return $response->json();
  }
}
