<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CFormwali extends Controller
{
  public function index()
  {
    return view('content.form-perwalian');
  }
}
