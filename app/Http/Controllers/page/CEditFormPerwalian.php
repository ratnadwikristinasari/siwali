<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CEditFormPerwalian extends Controller
{
    public function index()
  {
        $token = Auth::user()->token;
      
       
    return view('content.editform-perwalian', compact('editformwali'));
  }
}
