<?php

namespace App\Helpers;


use App\Models\Advise;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DashboardHelper {
 public static function rataIpkMahasiswa()
{
    
  $avgIpk = Advise::where('student_user_id', Auth::id())
        -> where('status', 'Done')
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
        ->whereNotNull('ipk');

    // FILTER SEMESTER
    if ($semester !== null) {
        $query->where('semester_id', $semester);
    }

    // DOSEN WALI
    if ($user->hasRole('lecture')) {
        $query->where('lecture_user_id', $user->id);
    }

    // KAPRODI
    if ($user->hasRole('kaprodi') && $user->prodi_id) {
        $query->whereHas('student', function ($q) use ($user) {
            $q->where('prodi_id', $user->prodi_id);
        });
    }

    /**
     * Ambil data TERAKHIR per mahasiswa
     */
    $data = $query
        ->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('advise')
                ->whereNotNull('ipk')
                ->groupBy('student_user_id', 'semester');
        })
        ->orderByDesc('ipk')
        ->limit(10)
        ->with('student:id,name')
        ->get();


    return [
        'categories' => $data->map(
            fn ($row) => $row->student->name ?? 'Unknown'
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
    if ($user->hasRole('lecture')) {
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
        ->map(fn ($s) => (int) $s)
        ->unique()
        ->sort()
        ->values();

        //Ambil IPK dari DB
        $ipk = Advise::where('student_user_id', $user->id)
            ->where('status','Done')
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


public static function totalPerwalianMahasiswa(): array
{
    $token = Auth::user()->token;

    $data = self::getAllMahasiswaWali($token);

    if ($data->isEmpty()) {
        return [
            'totalMahasiswa' => 0,
            'totalBelum' => 0,
            'totalPending' => 0,
            'totalDone' => 0,
        ];
    }

    $studentIds = $data->pluck('student_id')->toArray();

    $advises = Advise::whereIn('student_id', $studentIds)
        ->select('student_id', 'status', 'created_at')
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('student_id')
        ->map(fn ($items) => strtolower($items->first()->status ?? ''));

    $data = $data->map(function ($mhs) use ($advises) {
        $mhs['status_perwalian'] = $advises[$mhs['student_id']] ?? null;
        return $mhs;
    });

    return [
        'totalMahasiswa' => $data->count(),
        'totalBelum'     => $data->where('status_perwalian', null)->count(),
        'totalPending'   => $data->where('status_perwalian', 'pending')->count(),
        'totalDone'      => $data->where('status_perwalian', 'done')->count(),
    ];
}

 

    




}
