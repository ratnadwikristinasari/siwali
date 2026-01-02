<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CNotifikasi extends Controller
{
    public function index() {
        $token = Auth::user()->token;
        return view('content.notifikasi');
    }
     
}
