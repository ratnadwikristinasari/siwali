<?php

namespace App\Http\Controllers\page;

use App\Helpers\AuthHelper;
use App\Helpers\MahasiswaHelper;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class Cbiodata extends Controller

{
    public function Biodata(Request $request){
        $user = Auth::user();
        $token = $user->token;
        $majorId = $user->major_id ?? $user->prodi_id; 

        $response = MahasiswaHelper::getMahasiswa($token, $majorId);
        //dd($response);
       
        return view('content.biodata', compact('response'));
       
    }
}
