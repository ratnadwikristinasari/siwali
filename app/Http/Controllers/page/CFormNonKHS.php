<?php

namespace App\Http\Controllers\page;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CFormNonKHS extends Controller
{
   public function index()
  {
        $token = Auth::user()->token;

        $dataAuth = AuthHelper::getauth('', $token);
          $student = $dataAuth['data']['student_detail'] ?? null;

          // Ambil data prodi
          $programStudi = $student['study_program_name']?? '_'; 

          //Semester Aktif
          $semesterAktif = '_';
          if (!empty($student['student_semester']) && is_array($student['student_semester'])) {
              foreach ($student['student_semester'] as $sem) {
                  if (!empty($sem['is_active']) && $sem ['is_active']=== true) {
                      $semesterAktif = $sem['semester_id'];
                      $kelas = $sem['semester'];
                      break;
                  }
              }
    }
      
       
    return view('content.form-perwalian-nonkhs', compact('programStudi', 'semesterAktif', 'kelas', 'programStudi'));
  }
}
