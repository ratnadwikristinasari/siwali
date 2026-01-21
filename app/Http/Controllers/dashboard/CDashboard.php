<?php

namespace App\Http\Controllers\dashboard;

use App\Helpers\DashboardHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CDashboard extends Controller
{
  public function index(Request $request)
  {
  $token = $request->user()->token;
//DASHBOARD MAHASISWA

  $rataIPK = DashboardHelper::rataIpkMahasiswa();
  $totalwali = DashboardHelper::totalPerwalian();

  $grafik = DashboardHelper::grafikIpkPerSemester($token);
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
  $listSemester = DashboardHelper::listSemesterMahasiswa();

    return view('content.dashboard.dashboard-main', compact(
      'rataIPK', 
      'totalwali',
      'semesterLabels',
      'valueipk',
      'totalperwalian',
      'ipkTopData',
      'listSemester',
      'semester'
      
      ));
  }
}
