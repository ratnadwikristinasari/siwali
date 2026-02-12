<?php

namespace App\Http\Controllers\page;

use App\Helpers\AuthHelper;
use App\Helpers\MahasiswaHelper;
use App\Http\Controllers\Controller;
use App\Models\Advise;
use Illuminate\Support\Facades\Auth;

class CFormwali extends Controller
{
    public function index()
    {
        $token = Auth::user()->token;

        $dataAuth = AuthHelper::getauth('', $token);
        $student = $dataAuth['data']['student_detail'] ?? null;
        $programStudi = $student['study_program_name'] ?? '_';

        $semesterAktif = '-';
        $kelas = '-';
        if (!empty($student['student_semester']) && is_array($student['student_semester'])) {
            foreach ($student['student_semester'] as $sem) {
                if (!empty($sem['is_active']) && $sem['is_active'] === true) {
                    $semesterAktif = $sem['semester_id'];
                    $kelas = $sem['semester'];
                    break;
                }
            }
        }

        $studentScore = MahasiswaHelper::getStudentScore(
            $token,
            $student['id'] ?? '',
            $semesterAktif
        );

        $currentGPA = MahasiswaHelper::calculateGPA($studentScore['data']['semesters'][0]['subjects'] ?? []);

        $hasAdvising = Advise::where('student_user_id', Auth::user()->id)
            ->where('semester_id', $semesterAktif)
            ->where('type', 'gpa_advising')
            ->exists();

        return view('content.form-perwalian', compact('programStudi', 'semesterAktif', 'kelas', 'currentGPA', 'hasAdvising'));
    }
}
