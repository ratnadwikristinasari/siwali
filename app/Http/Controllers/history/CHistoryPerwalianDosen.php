<?php

namespace App\Http\Controllers\history;

use App\Http\Controllers\Controller;
use App\Models\Advise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CHistoryPerwalianDosen extends Controller
{
     public function index(Request $request)
  {
   $user = Auth::user();
   //Data perwalian Mahasiswa
   $perwaliandosen = Advise::
    where('lecture_user_id', $user->id)
    ->orderBy('status', 'desc')
    ->get();

    return view('content.dataperwalian-dosen', compact('perwaliandosen'));
  }
}
