<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function get(string|null $path, string|null $filename = '')
    {
        if ($path === null && $filename === null) return null;

        $fullPath = $path . '/' . $filename;

        if (empty($filename)) {
            $fullPath = $path;
        }

        return config('filesystems.disks.s3.url') . '/' . $fullPath; // Use S3 URL
    }

    public static function storeFile(UploadedFile $file, string $path, ?string $filename = ''): string
    {
        if (empty($filename)) {
            $filename = Str::random() . '.' . $file->getClientOriginalExtension();
        }

        $file->storeAs($path, $filename, 's3'); // Store in S3

        return $filename;
    }

    public static function deleteFile(string $path, ?string $filename): string
    {
        if ($filename === null) return '';

        $fullPath = $path . '/' . $filename;

        if (Storage::disk('s3')->exists($fullPath)) { // Check existence in S3
            Storage::disk('s3')->delete($fullPath); // Delete from S3

            return $filename;
        }

        return '';
    }
}
