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
    $response = Http::withoutVerifying()->withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])
      ->withQueryParameters($queryParams)
      ->get(config('app.super_app_url') . '/supervisor-lectures/by-lecture');

    return $response->json();
  }

public static function getMahasiswaByProdi(string $token, ?string $prodiId = null, ?int $page = 1): array 
{

        $queryParams = [
            'page' => $page
        ];

        if (!empty($prodiId)) {
            $queryParams['study_program_id'] = $prodiId;
        }

        $response = Http::withToken($token)
            ->get(config('app.super_app_url') . '/students', $queryParams);

        return $response->json();
    }
}

