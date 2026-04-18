<?php

namespace App\Helpers;


use App\Http\Controllers\page\CProdi;
use App\Models\Advise;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DashboardHelper
{
    public static function rataIpkMahasiswa()
    {

        $avgIpk = Advise::where('student_user_id', Auth::id())
            ->where('status', 'Done')
            ->whereNotNull('ipk')
            ->avg('ipk');

        return min(round($avgIpk ?? 0, 2), 4.00);
    }
    public static function totalPerwalian()
    {
        return Advise::where('student_user_id', Auth::id())
            ->where('status', 'Done')
            ->count();
    }
    public static function topIpkMahasiswa(?string $semester = null): array
    {
        $user = Auth::user();

        $query = Advise::query()
            ->where('status', 'done')
            ->where('type', 'gpa_advising')
            ->whereNotNull('ipk');

        // FILTER SEMESTER
        if ($semester !== null) {
            $query->where('semester_id', $semester);
        }

        // DOSEN WALI
        if ($user->hasAnyRole(['lecturer', 'lecture'])) {
            $query->where('lecture_user_id', $user->id);
        }

        // FILTER PRODI (for kajur)
        if ($prodi = request()->input('prodi_id')) {
            $query->whereHas('student', function ($q) use ($prodi) {
                $q->where('study_program_id', $prodi);
            });
        }

        // KAPRODI - filter by student's study_program_id
        if ($user->hasRole('kaprodi') && !empty($user->study_program_id)) {
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('study_program_id', $user->study_program_id);
            });
        }

        // KAJUR - tanpa filter tambahan, semua mahasiswa di jurusannya

        /**
         * Ambil data TERAKHIR per mahasiswa
         */
        $data = $query
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('advise')
                    ->where('status', 'done')
                    ->where('type', 'gpa_advising')
                    ->whereNotNull('ipk')
                    ->groupBy('student_id');
            })
            ->orderByDesc('ipk')
            ->limit(10)
            ->with('student:id,name')
            ->get();

        return [
            'categories' => $data->map(
                fn($row) => $row->student->name ?? 'Unknown'
            )->values(),

            'series' => $data->pluck('ipk')->values(),
        ];
    }

    public static function listSemesterMahasiswa(): array
    {
        $user = Auth::user();

        $query = Advise::query()
            ->whereNotNull('semester');

        // DOSEN WALI
        if ($user->hasAnyRole(['lecturer', 'lecture'])) {
            $query->where('lecture_user_id', $user->id);
        }

        // KAPRODI
        if ($user->hasRole('kaprodi') && $user->prodi_id) {
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('prodi_id', $user->prodi_id);
            });
        }

        // KAJUR → tanpa filter tambahan

        return $query

            ->distinct()
            ->orderBy('semester')
            ->pluck('semester')
            ->toArray();
    }


    public static function grafikIpkPerSemester(string $token)
    {

        if (empty($token)) {
            return collect([]);
        }

        $user = Auth::user();

        $auth = AuthHelper::getauth('', $user->token);
        $datasemester = collect($auth['data']['student_detail']['student_semester'] ?? [])

            ->pluck('semester')   // ambil semester_id
            ->filter()               // buang null
            ->map(fn($s) => (int) $s)
            ->unique()
            ->sort()
            ->values();

        //Ambil IPK dari DB
        $ipk = Advise::where('student_user_id', $user->id)
            ->where('status', 'Done')
            ->whereNotNull('ipk')
            ->get()
            ->mapWithKeys(function ($row) {
                return [(int) $row->semester => $row->ipk];
            });

        //Sinkronkan semester + IPK
        return $datasemester->map(function ($semester) use ($ipk) {
            return [
                'semester' => 'Semester ' . $semester,
                'ipk' => min(max(round($ipk[$semester] ?? 0, 2), 0), 4),
            ];
        });
    }
    //DASHBOARD DOSEN

    public static function getAllMahasiswaWali(string $token, ?string $majorId = null)
    {
        $page = 1;
        $allData = collect();

        do {
            $response = MahasiswaHelper::getMahasiswa($token, $majorId, $page);

            if (empty($response['data'])) {
                break;
            }

            $allData = $allData->merge($response['data']);

            $meta = $response['meta'] ?? [];

            if (isset($meta['last_page'])) {
                $lastPage = $meta['last_page'];
            } elseif (isset($meta['total'], $meta['per_page'])) {
                $lastPage = (int) ceil($meta['total'] / $meta['per_page']);
            } else {
                $lastPage = $page;
            }

            $page++;
        } while ($page <= $lastPage);

        return $allData;
    }


    public static function totalPerwalianMahasiswa(?string $semesterId = null): array
    {
        $user = Auth::user();

        $totalMahasiswa = 0;
        $metaResponse = MahasiswaHelper::getMahasiswa(
            $user->token,
            $user->major_id,
            1,
            ''
        );

        if (!empty($metaResponse['meta']['total'])) {
            $totalMahasiswa = (int) $metaResponse['meta']['total'];
        }

        $adviseQuery = Advise::query()
            ->where('lecture_user_id', $user->id)
            ->where('type', 'gpa_advising');

        if (!empty($semesterId)) {
            $adviseQuery->where('semester_id', $semesterId);
        }

        $totalPending = (clone $adviseQuery)
            ->where('status', 'pending')
            ->count();

        // signed dianggap selesai pada proses perwalian dosen
        $totalDone = (clone $adviseQuery)
            ->whereIn('status', ['done', 'signed'])
            ->count();

        $totalSudahPerwalian = (clone $adviseQuery)
            ->distinct('student_id')
            ->count('student_id');

        return [
            'totalMahasiswa' => $totalMahasiswa,
            'totalBelum' => max(0, $totalMahasiswa - $totalSudahPerwalian),
            'totalPending' => $totalPending,
            'totalDone' => $totalDone,
        ];
    }


    public static function getAnalytics(array $filters = []): array
    {
        $user = Auth::user();

        $response = GlobalHelper::requestWithToken(
            url: '/analytics/dashboard',
            token: $user->token,
            method: 'GET',
            queryParams: $filters
        );

        return $response['data'] ?? [];
    }
}
