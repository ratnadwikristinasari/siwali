<?php

namespace App\Http\Controllers\history;

use App\Http\Controllers\Controller;
use App\Models\Advise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CEditFormwali extends Controller
{
   public function index(Request $request)
  {
   $user = Auth::user();
   //Data perwalian Mahasiswa
   $perwalian = Advise::
    where('lecture_user_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->get();
    return view('content.editform-perwalian', compact('perwaliandosen'));
  }
}
