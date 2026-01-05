<?php

namespace App\Http\Controllers\page;


use App\Helpers\AuthHelper;
use App\Helpers\DosenHelper;
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
            'khs'=>'nullable|file|mimes:pdf|max:1000',
        ]);
        $studentUser= Auth::user();
        $dataAuth = AuthHelper::getauth('', $studentUser->token);
        //dd($dataAuth);
        $advisor = collect($dataAuth['data']['student_detail']['supervisor_lectures'])->firstWhere('position', 'ACADEMIC_ADVISOR');
        if (!$advisor) {
        return back()->withErrors('Dosen Wali tidak ditemukan');
    }

        $employeeId = $advisor['employee_id'];
        $datadosen = DosenHelper::getdosenById($studentUser->token, $employeeId);
        
        $lectureUser = User::updateOrCreate(['external_id' => $datadosen['data']['user']['id']], 
        [
            'name'=>$datadosen['data']['user']['name'],
            'email' =>$datadosen['data']['user']['email']
        ]);
        if (!$lectureUser) {
            return back()->withErrors('User Dosen Wali tidak ditemukan');
        }
        $khsfile =null;
        if ($request->hasFile('khs')) {
            $file = $request->file('khs');
            $filename = time().'_'.$file->getClientOriginalName();

            //Simpan ke storage/app/public/khs
            $khsfile = $file->storeAs('khs', $filename, 'public');
        }


        //dd($lecture);
        Advise::create([
            'student_user_id' => $studentUser->id,
            'lecture_user_id' => $lectureUser->id,
            'student_id'=> $dataAuth['data']['student_detail']['id'],
            'lecture_id'=>$employeeId,
            'status' => 'pending',
            'khs' => $khsfile,
            'ipk' => $request->ipk,
            'keluhan' => $request->keluhan,         
        ]);

        session()->forget('khs_file');
        
        return redirect()
            ->route('dataperwalian')
            ->with('success', 'Perwalian Berhasil Diajukan');
    }
}

