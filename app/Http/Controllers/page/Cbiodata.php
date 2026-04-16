<?php

namespace App\Http\Controllers\page;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Cbiodata extends Controller
{
    public function Biodata(Request $request){
        
        $user = Auth::user();
        $userToken = $user->token; 
        $majorId = $request->input('majorId'); 
        $userData = AuthHelper::getauth($majorId, $userToken);
       
        //dd($userData);
        return view('content.biodata', compact('userData'));
    }
}