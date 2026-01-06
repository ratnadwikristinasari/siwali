<?php

namespace App\Http\Controllers;

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
        ]);
        
        $file = $request->file('file');
        $filename = time().'_'.$file->getClientOriginalName();
        $path = $file->storeAs('khs', $filename, 'public');
        session(['khs_file' => $path]); //simpan ke session
        return response()->json(['success'=> true]);
    }
}
