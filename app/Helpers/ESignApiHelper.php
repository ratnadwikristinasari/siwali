<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class ESignApiHelper
{
    public static function signDocument(string $token, string $docName, string $signerName)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->post(config('app.super_app_url_internal') . '/esign/sign-document', [
                'document_name' => $docName,
                'signer_name' => $signerName,
                'source_app' => config('app.name'),
            ]);

        return $response->json();
    }

    public static function updateDocumentHash(string $token, string $docId, string $newHash)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->put(config('app.super_app_url_internal') . '/esign/update-document-hash/' . $docId, [
                'document_hash' => $newHash,
            ]);

        return $response->json();
    }

    public static function signPDF(string $token, string $pdfContent, string $signerName, ?string $signedBy = 'Ketua Jurusan')
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->attach(
                'file',
                $pdfContent,
                'document.pdf'
            )
            ->post(config('app.super_app_url_internal') . '/esign/sign-pdf', [
                'signer_name' => $signerName,
                'signed_by' => $signedBy,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to sign document' . $response->body());
        }

        return $response->body();
    }
}
