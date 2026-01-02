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
        $file = $request->file('file');
        $fileName = time().rand(1,100).'.'.$file->extension();
        $file->move(public_path('files'), $fileName);
        return response()->json(['success'=>$fileName]);
    }
}
