<?php

namespace App\Http\Controllers\page;

use App\Helpers\AuthHelper;
use App\Helpers\DosenHelper;
use App\Http\Controllers\Controller;
use App\Mail\PengajuanDiterimaMail;
use App\Models\Advise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CperwalianNonKHS extends Controller
{
    public function storekhs(Request $request)
    {
        $request->validate([
            'keluhan' => 'nullable|string'
        ]);
        $studentUser = Auth::user();
        $dataAuth = AuthHelper::getauth('', $studentUser->token);

        $advisor = collect($dataAuth['data']['student_detail']['supervisor_lectures'])->firstWhere('position', 'ACADEMIC_ADVISOR');
        if (!$advisor) {
            return back()->withErrors('Dosen Wali tidak ditemukan');
        }

        $employeeId = $advisor['employee_id'];
        $datadosen = DosenHelper::getdosenById($studentUser->token, $employeeId);

        $lectureUser = User::updateOrCreate(
            ['external_id' => $datadosen['data']['user']['id']],
            [
                'name' => $datadosen['data']['user']['name'],
                'email' => $datadosen['data']['user']['email']
            ]
        );
        if (!$lectureUser) {
            return back()->withErrors([
                'advisor' => 'Dosen Wali tidak ditemukan'
            ]);
        }

        // $status = empty($request->masukan) ? 'pending' : 'done';

        $activeSemester = array_values(array_filter(
            $dataAuth['data']['student_detail']['student_semester'],
            fn($s) => $s['is_active'] === true
        ));


        $semesterId = $activeSemester[0]['semester_id'] ?? null;
        $semesterAktif = $activeSemester[0]['semester'] ?? null;

        if (!$semesterAktif) {
            return back()->withErrors('Semester aktif tidak ditemukan');
        }

        $studyProgramId = data_get($dataAuth, 'data.student_detail.m_study_program_id');
        $studyProgramName = data_get($dataAuth, 'data.student_detail.study_program_name');

        if (!$studyProgramId) {
            return back()->withErrors('Program Studi tidak ditemukan dari data user');
        }
        $majorId   = data_get($dataAuth, 'data.student_detail.m_major_id');
        $majorName = data_get($dataAuth, 'data.student_detail.major_name');

        if (!$majorId) {
            return back()->withErrors('Jurusan (Major) tidak ditemukan dari data user');
        }

        //dd($lecture);
        $wali = Advise::create([
            'student_user_id' => $studentUser->id,
            'lecture_user_id' => $lectureUser->id,
            'student_id' => $dataAuth['data']['student_detail']['id'],
            'lecture_id' => $employeeId,
            'status' => empty($request->masukan) ? 'pending' : 'done',
            'semester' => $semesterAktif,
            'study_program' => $studyProgramName,
            'major' => $majorName,
            'keluhan' => $request->keluhan,
            'semester_id' => $semesterId,

            // 'session_id' => $dataAuth
        ]);
        return redirect()
            ->route('dataperwalian')
            ->with('Success', 'Perwalian Berhasil Diajukan');
    }

    public function edit($id)
    {
        $perwalian = Advise::findOrFail($id);
        return view('content.editform-perwalian', compact('perwalian'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'masukan' => 'nullable|string',
        ]);

        $perwalian = Advise::findOrFail($id);
        $newstatus = empty($request->masukan) ? 'pending' : 'done';
        $perwalian->update([
            'masukan' => $request->masukan,
            'status'  => $newstatus,
        ]);

        $perwalian->load('student', 'lecture');

        if ($newstatus === 'done') {
            Mail::to($perwalian->student->email)
                ->queue(new PengajuanDiterimaMail($perwalian));
        }

        return redirect()
            ->route('dataperwaliandosen')
            ->with('success', 'Perwalian Selesai!');
    }
}
