<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class CheckJtiformStatusHelper
{
    public static function check(string $sessionId, bool $isEven, string $externalId): ?bool
    {
        $baseUrl = (string) config('services.jtiform.base_url', '');
        $apiKey = (string) config('services.interservice.api_key', '');

        if ($baseUrl === '' || $apiKey === '') {
            return null;
        }

        $response = Http::acceptJson()
            ->withHeaders([
                'X-API-KEY' => $apiKey,
            ])
            ->post(
                $baseUrl . '/check-status',
                [
                    'session_id' => $sessionId,
                    'is_even' => $isEven,
                    'external_id' => $externalId,
                ]
            );

        if (!$response->successful()) {
            return null;
        }

        return (bool) data_get($response->json(), 'is_filled', false);
    }
}
