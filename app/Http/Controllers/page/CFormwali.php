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

        $semesters = [];
        $gpaData = [];
        $khsDoneStatus = [];

        if (!empty($student['student_semester']) && is_array($student['student_semester'])) {
            foreach ($student['student_semester'] as $sem) {
                $semId = $sem['semester_id'];

                // Simpan daftar semester
                $semesters[] = [
                    'semester_id' => $semId,
                    'semester'    => $sem['semester'],
                    'is_active'   => $sem['is_active'] ?? false
                ];

                // Cek apakah pengajuan KHS di semester ini sudah "done"
                $advisingDone = Advise::where('student_user_id', Auth::user()->id)
                    ->where('semester_id', $semId)
                    ->where('type', 'gpa_advising')
                    ->where('status', 'done')
                    ->exists();

                $khsDoneStatuses[$semId] = $advisingDone;

                $studentScore = MahasiswaHelper::getStudentScore($token, $student['id'] ?? '', $semId);
                $gpaData[$semId] = MahasiswaHelper::calculateGPA($studentScore['data']['semesters'][0]['subjects'] ?? []);
            }
        }

        return view('content.form-perwalian', compact(
            'programStudi',
            'semesters',
            'gpaData',
            'khsDoneStatuses'
        ));
    }
}
