<?php

namespace App\Http\Controllers;

use App\Helpers\MajorHelper;
use App\Helpers\ProdiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CMajor extends Controller
{
     public function index()
  {
        $token = Auth::user()->token;
      
       
    return view('content.dataprodi', compact('majors'));
  }
}
