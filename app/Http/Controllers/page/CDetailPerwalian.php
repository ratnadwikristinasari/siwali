<?php

namespace App\Http\Controllers\page;

use App\Helpers\AuthHelper;
use App\Helpers\DosenHelper;
use App\Helpers\MahasiswaHelper;
use App\Helpers\MajorHelper;
use App\Helpers\SemesterApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Advise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CDetailPerwalian extends Controller
{
    public function detail(Request $request, $id)
    {
        $token = Auth::user()->token;
        $authData = AuthHelper::getauth(null, $token)['data'] ?? [];

        $perwalian = Advise::with([
            'student',
            'lecture'
        ])->findOrFail($id);

        if ($perwalian->type === 'non_gpa_advising') {
            return view('content.detailperwaliannokhs', compact('perwalian'));
        }

        $studentScore = MahasiswaHelper::getStudentScore(
            $token,
            $perwalian->student_id ?? '',
            $perwalian->semester_id
        );

        $majorHeadData = MajorHelper::getById(
            $token,
            $perwalian->lecture->major_id ?? ''
        );

        $academicYear = SemesterApiHelper::getById(
            $token,
            $perwalian->semester_id ?? ''
        );

        $supervisorData = DosenHelper::getLectureByUserId(
            $token,
            $perwalian->lecture->external_id ?? ''
        );

        $lectureName = $supervisorData['user']['name'] ?? 'N/A';
        $lecturerNip = $supervisorData['nip'] ?? 'N/A';

        $data = $studentScore['data'] ?? [];
        $pdf = Pdf::loadView('content.khs.khs-pdf', compact(
            'data',
            'academicYear',
            'majorHeadData',
            'authData',
            'lectureName',
            'lecturerNip'
        ))->setPaper('a4', 'portrait');

        $perwalian->khs_pdf = $pdf->output();

        return view('content.detailperwalian', compact('perwalian', 'lectureName', 'lecturerNip'));
    }
}
