<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CDetailMahasiswa extends Controller
{
    public function index() {
        $user = Auth::user();
        
        return view('content.detailmahasiswa');
    }
     
}
