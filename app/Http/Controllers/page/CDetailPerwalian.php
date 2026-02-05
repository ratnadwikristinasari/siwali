<?php

namespace App\Http\Controllers\page;

use App\Http\Controllers\Controller;
use App\Models\Advise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CDetailPerwalian extends Controller
{
        public function detail(Request $request, $id)
  {
        $token = Auth::user()->token;
       $perwalian = Advise::with([
            'student',
            'lecture'
        ])->findOrFail($id);


        return view('content.detailperwalian', compact('perwalian'));
  }
}
