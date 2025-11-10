<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CDashboard extends Controller
{
  public function index()
  {
    return view('content.kaprodi.dashboard');
  }
}
