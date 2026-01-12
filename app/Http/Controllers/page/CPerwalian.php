<?php

namespace App\Http\Controllers\page;


use App\Helpers\AuthHelper;
use App\Helpers\DosenHelper;
use App\Http\Controllers\Controller;
use App\Mail\AjukanPerwalianMail;
use App\Models\Advise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
            return back()->withErrors([
                'advisor' => 'Dosen Wali tidak ditemukan'
            ]);
        }
        $khsfile = session('khs_file');
        
        if (!$khsfile) {
            return back()->withErrors([
                'khsfile' => 'KHS belum diupload'
            ]);
        }

        $status = empty($request->masukan) ? 'Pending' : 'Done';
      
        //dd($lecture);
        $wali= Advise::create([
            'student_user_id' => $studentUser->id,
            'lecture_user_id' => $lectureUser->id,
            'student_id'=> $dataAuth['data']['student_detail']['id'],
            'lecture_id'=>$employeeId,
            'status' => empty($request->masukan) ? 'Pending' : 'Done',
            'khs' => $khsfile,
            'ipk' => $request->ipk,
            'keluhan' => $request->keluhan,         
        ]);

        $wali->load('student', 'lecture');
        
    
        if ($status === 'Pending') {
        Mail::to($lectureUser->email)
        ->queue(new AjukanPerwalianMail($wali));
        }
        session()->forget('khs_file');
        
        return redirect()
            ->route('dataperwalian')
            ->with('Success', 'Perwalian Berhasil Diajukan');
    }

    public function edit($id) {
        $perwalian = Advise::findOrFail($id);
        return view('content.editform-perwalian', compact('perwalian'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'masukan' => 'nullable|string',
    ]);

    $perwalian = Advise::findOrFail($id);
    $perwalian->update([
        'masukan' => $request->masukan,
        'status'  => empty($request->masukan) ? 'pending' : 'done',
    ]);

    return redirect()
        ->route('dataperwaliandosen')
        ->with('success', 'Perwalian Selesai!');
}

}

