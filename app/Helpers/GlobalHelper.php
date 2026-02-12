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
            $response = $response->post(config('app.super_app_url_internal') . $url, $data);
        } else {
            $response = $response->get(config('app.super_app_url_internal') . $url, $data);
        }

        return $response;
    }
}
