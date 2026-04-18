<?php

namespace App\Http\Controllers\dashboard;

use App\Helpers\DashboardHelper;
use App\Helpers\ProdiHelper;
use App\Helpers\SemesterApiHelper;
use App\Helpers\SessionApiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CDashboard extends Controller
{
    private const EMPTY_ANALYTICS_SUMMARY = [
        'total_employees' => 0,
        'total_employees_active' => 0,
        'total_lecturers' => 0,
        'total_students' => 0,
        'total_students_active' => 0,
        'total_study_programs' => 0,
        'total_majors' => 0,
    ];

    public function index(Request $request)
    {
        $user = $request->user();
        $token = $user->token;

        // Default kosong untuk role selain dashboard yang aktif agar beban query ringan.
        $rataIPK = 0;
        $totalwali = 0;
        $semesterLabels = [];
        $valueipk = [];
        $totalperwalian = [
            'totalMahasiswa' => 0,
            'totalBelum' => 0,
            'totalPending' => 0,
            'totalDone' => 0,
        ];
        $semester = $request->query('semester');
        $ipkTopData = [
            'categories' => collect([]),
            'series' => collect([]),
        ];
        $sessions = [];
        $listSemester = [];
        $prodiList = [];

        // Aktifkan Top IPK untuk kajur, kaprodi, dan dosen
        if ($user->hasAnyRole(['kajur', 'kaprodi', 'lecturer'])) {
            $ipkTopData = DashboardHelper::topIpkMahasiswa($semester);
            $sessions = SessionApiHelper::getAsOptions($token);
            $listSemester = DashboardHelper::listSemesterMahasiswa();

            // Fetch list prodi untuk kajur saja
            if ($user->hasRole('kajur') && !empty($user->major_id)) {
                try {
                    $prodiResponse = ProdiHelper::getprodi($token, $user->major_id, 1, null);
                    if (!empty($prodiResponse['data'])) {
                        $prodiList = array_map(function ($item) {
                            return [
                                'value' => $item['id'],
                                'label' => $item['name']
                            ];
                        }, $prodiResponse['data']);
                    }
                } catch (\Exception $e) {
                    // Silent fail - prodi list optional
                }
            }
        }

        // Statistik perwalian untuk dosen, bisa terfilter semester_id.
        if ($user->hasRole('lecturer')) {
            $totalperwalian = DashboardHelper::totalPerwalianMahasiswa(
                $request->query('semester_id')
            );
        }

        $analytics = [];
        $analyticsSummary = self::EMPTY_ANALYTICS_SUMMARY;

        if ($user->hasAnyRole(['kajur', 'kaprodi'])) {
            $analyticsFilters = [
                'include_breakdown' => true,
            ];

            if ($user->hasRole('kajur') && !empty($user->major_id)) {
                $analyticsFilters['major_id'] = $user->major_id;
            }

            if ($user->hasRole('kaprodi') && !empty($user->study_program_id)) {
                $analyticsFilters['study_program_id'] = $user->study_program_id;
            }
            $analytics = DashboardHelper::getAnalytics($analyticsFilters);
            $analyticsSummary = array_merge(
                self::EMPTY_ANALYTICS_SUMMARY,
                Arr::get($analytics, 'summary', [])
            );
        }

        return view('content.dashboard.dashboard-main', compact(
            'rataIPK',
            'totalwali',
            'semesterLabels',
            'valueipk',
            'totalperwalian',
            'ipkTopData',
            'listSemester',
            'semester',
            'sessions',
            'prodiList',
            'analytics',
            'analyticsSummary'

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
        $semesterId = $request->input('semester_id');
        $ipkTopData = DashboardHelper::topIpkMahasiswa($semesterId);

        return response()->json([
            'categories' => $ipkTopData['categories']->values(),
            'series' => $ipkTopData['series']->values(),
        ]);
    }

    public function getLecturerAnalytics(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('lecturer')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $summary = DashboardHelper::totalPerwalianMahasiswa(
            $request->input('semester_id')
        );

        return response()->json($summary);
    }
}
