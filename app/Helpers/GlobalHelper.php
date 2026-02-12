<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class GlobalHelper
{
    public static function requestWithToken($url, $token, $method = 'GET', $data = [], $queryParams = []): \Illuminate\Http\Client\Response | \GuzzleHttp\Promise\PromiseInterface
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->withQueryParameters($queryParams);

        if (strtoupper($method) === 'POST') {
            $response = $response->post($url, $data);
        } else {
            $response = $response->get($url, $data);
        }

        return $response;
    }
}
