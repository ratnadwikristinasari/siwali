<?php

namespace App\Helpers;

use App\Models\Advise;
use App\Models\StudentParent;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DashboardHelper
{
    public static function getDashboardStudentUser(): ?User
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        if ($user->hasRole('student')) {
            return $user;
        }

        if ($user->hasRole('orang_tua')) {
            $studentParent = StudentParent::where('parent_id', $user->id)->first();
            return $studentParent?->student;
        }

        return null;
    }

    public static function rataIpkMahasiswa(?string $studentUserId = null)
    {
        $studentUserId ??= self::getDashboardStudentUser()?->id;

        if (empty($studentUserId)) {
            return 0;
        }

        $avgIpk = Advise::where('student_user_id', $studentUserId)
            ->where('type', 'gpa_advising')
            ->whereIn('status', ['done', 'signed'])
            ->whereNotNull('ipk')
            ->avg('ipk');

        return min(round($avgIpk ?? 0, 2), 4.00);
    }
    public static function totalPerwalian(?string $studentUserId = null)
    {
        $studentUserId ??= self::getDashboardStudentUser()?->id;

        if (empty($studentUserId)) {
            return 0;
        }

        return Advise::where('student_user_id', $studentUserId)
            ->where('type', 'gpa_advising')
            ->whereIn('status', ['done', 'signed'])
            ->count();
    }
    public static function totalNonPerwalian(?string $studentUserId = null)
    {
        $studentUserId ??= self::getDashboardStudentUser()?->id;

        if (empty($studentUserId)) {
            return 0;
        }

        return Advise::where('student_user_id', $studentUserId)
            ->where('type', 'non_gpa_advising')
            ->whereIn('status', ['done'])
            ->count();
    }
    public static function topIpkMahasiswa(?string $semester = null, bool $isFromMyDashboard = false): array
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
        if ($user->hasAnyRole(['lecturer']) && $user->hasNoRole(['kaprodi', 'kajur'])) {
            $query->where('lecture_user_id', $user->id);
        }

        if ($isFromMyDashboard) {
            if ($user->hasRole('lecturer')) {
                $query->where('lecture_user_id', $user->id);
            }
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
            ->whereIn('advise.id', function ($sub) {
                $sub->selectRaw('MAX(advise.id)')
                    ->from('advise')
                    ->where('status', 'done')
                    ->where('type', 'gpa_advising')
                    ->whereNotNull('ipk')
                    ->groupBy('student_id');
            })
            ->join('users', 'advise.student_user_id', '=', 'users.id')
            ->orderByDesc('advise.ipk')
            ->orderBy('users.name', 'asc')
            ->limit(10)
            ->with('student:id,name')
            ->select('advise.*')
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


    public static function grafikIpkPerSemester(?string $studentUserId = null): Collection
    {
        $studentUserId ??= self::getDashboardStudentUser()?->id;

        if (empty($studentUserId)) {
            return collect([]);
        }

        return Advise::where('student_user_id', $studentUserId)
            ->where('type', 'gpa_advising')
            ->whereIn('status', ['done', 'signed'])
            ->whereNotNull('ipk')
            ->whereNotNull('semester')
            ->orderBy('semester')
            ->get(['semester', 'ipk'])
            ->groupBy('semester')
            ->map(function ($items, $semester) {
                $latest = $items->last();
                return [
                    'semester' => 'Semester ' . $semester,
                    'ipk' => min(max(round((float) ($latest->ipk ?? 0), 2), 0), 4),
                ];
            })
            ->values();
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
