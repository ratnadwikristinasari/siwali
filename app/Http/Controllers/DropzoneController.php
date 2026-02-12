<?php

namespace App\Http\Controllers;

use App\Helpers\FileHelper;
use Illuminate\Http\Request;

class DropzoneController extends Controller
{
    //To render html form upload
    public function index()
    {
        return view('content.form-perwalian');
    }

    //To Upload file from client to server
    public function khs(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:1024'
        ], [
            'file.required' => 'KHS Wajib Diupload',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $filename = FileHelper::storeFile($file, 'khs_files', $filename);

        $fullUrl = FileHelper::get('khs_files', $filename);

        $oldFile = session('khs_file');
        if ($oldFile) {
            FileHelper::deleteFile('khs_files', $oldFile);
        }

        session(['khs_file' => $filename]);

        return response()->json([
            'success' => true,
            'path' => 'khs_files/' . $filename,
            'url' => $fullUrl
        ]);
    }
}
