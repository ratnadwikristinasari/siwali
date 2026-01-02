<?php

namespace App\Http\Controllers\page;


use App\Http\Controllers\Controller;
use App\Models\Advise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CPerwalian extends Controller
{
    public function store(Request $request) 
    {
        $request->validate([
            'ipk'=>'nullable|numeric',
            'keluhan'=>'nullable|string',
        ]);
        $student= Auth::user();

        //contoh doswal sementara
        $lecture= User::whereJsonContains('roles', 'dosen')->first();

        Advise::create([
            'student_id' => $student->id,
            'lecture_id' => $lecture?->id,
            'status' => 'pending',
            'khs' => session('khs_file'),
            'ipk' => $request->ipk,
            'keluhan' => $request->keluhan,         
        ]);

        session()->forget('khs_file');
        
        return redirect()
            ->route('dataperwalian')
            ->with('success', 'Perwalian Berhasil Diajukan');
    }
}

