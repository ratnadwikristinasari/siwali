<?php

namespace App\Http\Controllers\dashboard;

use App\Helpers\AuthHelper;
use App\Helpers\DashboardHelper;
use App\Helpers\SemesterApiHelper;
use App\Helpers\SessionApiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CDashboard extends Controller
{
    public function index(Request $request)
    {
        $token = $request->user()->token;
        //DASHBOARD MAHASISWA
        $studentUser = Auth::user();
        $rataIPK = DashboardHelper::rataIpkMahasiswa();
        $totalwali = DashboardHelper::totalPerwalian();

        $grafik = DashboardHelper::grafikIpkPerSemester($token);
        $dataAuth = AuthHelper::getauth('', $studentUser->token);
        //Grafik IPK
        $semesterLabels = [];
        $valueipk = [];

        if ($request->user()->hasRole('student')) {
            $grafik = DashboardHelper::grafikIpkPerSemester($token);

            $semesterLabels = $grafik->pluck('semester')->toArray();
            $valueipk        = $grafik->pluck('ipk')->toArray();
        }
        //DASHBOARD DOSEN
        $totalperwalian = DashboardHelper::totalPerwalianMahasiswa();
        $semester = $request->query('semester');
        $ipkTopData = DashboardHelper::topIpkMahasiswa();
        $sessions = SessionApiHelper::getAsOptions($token);
        $listSemester = DashboardHelper::listSemesterMahasiswa();

        return view('content.dashboard.dashboard-main', compact(
            'rataIPK',
            'totalwali',
            'semesterLabels',
            'valueipk',
            'totalperwalian',
            'ipkTopData',
            'listSemester',
            'semester',
            'sessions'

        ));
    }
    public function myDashboard(Request $request)
    {
        $token = Auth::user()->token;
        $totalperwalian = DashboardHelper::totalPerwalianMahasiswa();
        $semester = $request->query('semester');
        $ipkTopData = DashboardHelper::topIpkMahasiswa();
        $sessions = SessionApiHelper::getAsOptions($token);
        $listSemester = SemesterApiHelper::getSemesterAsOption($token);

        // dd($semester, $ipkTopData, $listSemester, $sessions);

        return view('content.dashboard.dashboard-my', compact(
            'totalperwalian',
            'ipkTopData',
            'listSemester',
            'semester',
            'sessions'
        ));
    }

    public function getTopTenStudent(Request $request)
    {
        $token = Auth::user()->token;
        $semesterId = $request->input('semester_id');

        $ipkTopData = DashboardHelper::topIpkMahasiswa($semesterId);

        return response()->json([
            'categories' => $ipkTopData['categories']->values(),
            'series' => $ipkTopData['series']->values(),
        ]);
    }
}
