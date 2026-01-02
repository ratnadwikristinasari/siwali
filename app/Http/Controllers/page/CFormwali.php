<?php

namespace App\Http\Controllers\page;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CFormwali extends Controller
{
  public function index()
  {
    $token = Auth::user()->token;   // token dari user login
    

    $dataAuth = AuthHelper::getauth('', $token);
    $student = $dataAuth['data']['student_detail'] ?? null;

    // Ambil data yang dibutuhkan
    $programStudi = $student['study_program_name']?? '_'; 

    //Semester Aktif
    $semesterAktif = '_';
    if (!empty($student['student_semester'])) {
        foreach ($student['student_semester'] as $sem) {
            if ($sem['is_active']=== true) {
                $semesterAktif = $sem['is_active'];
            }
        }
    }

    

    return view('content.form-perwalian', compact('programStudi','semesterAktif'));
  }
}
