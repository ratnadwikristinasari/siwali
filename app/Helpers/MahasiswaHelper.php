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

  public static function getAllMahasiswaWali(string $token, ?string $majorId = null)
{
    $page = 1;
    $allData = collect();

    do {
        $response = MahasiswaHelper::getMahasiswa($token, $majorId, $page);

        if (empty($response['data'])) {
            break;
        }

        $allData = $allData->merge($response['data']);

        $meta = $response['meta'] ?? [];

        if (isset($meta['last_page'])) {
            $lastPage = $meta['last_page'];
        } elseif (isset($meta['total'], $meta['per_page'])) {
            $lastPage = (int) ceil($meta['total'] / $meta['per_page']);
        } else {
            $lastPage = $page;
        }

        $page++;
    } while ($page <= $lastPage);

    return $allData;
}

  public static function getMahasiswaByProdi(string $token, ?string $majorId)
{
    if (empty($majorId)) {
        return collect();
    }

    return collect(Http::withToken($token)
        ->get(config('app.super_app_url') . '/students', [
            'major_id' => $majorId
        ])
        ->json('data'));
}


public static function getMahasiswaByJurusan(string $token, string $majorId)
    {
        return collect(Http::withToken($token)
            ->get(config('app.super_app_url') . 'supervisor-lectures/by-lecture', [
                'major_id' => $majorId
            ])
            ->json('data'));
    }
}

