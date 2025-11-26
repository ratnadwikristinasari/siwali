<?php

namespace App\Http\Controllers\page;

use App\Helpers\MajorHelper;
use App\Helpers\ProdiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CProdi extends Controller
{
  public function index()
  {
        $token = Auth::user()->token;
        $prodis = ProdiHelper::getprodi($token );
        dd($prodis);
    return view('content.dataprodi', compact('prodis'));
  }
}

