<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CDosen extends Controller
{
  public function index()
  {
    return view('content.datadosen');
  }
}

