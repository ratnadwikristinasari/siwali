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
public static function topIpkMahasiswa()
{
  $user = Auth::user();

  $query = Advise::select('student_user_id')
            ->selectRaw('MAX(ipk) as ipk')
            ->where('status','Done')
            ->whereNotNull('ipk');

  //DOSEN BIMBINGAN MAHASISWA
  if (in_array('lecture', $user->roles)) {
  $query->where('lecture_user_id', $user->id);
  }

  //KAPRODI MAHASISWA PRODI
  if ($user->hasRole('kaprodi') && $user->prodi_id) {
    $query->whereHas('student', function ($q) use ($user) {
        $q->where('prodi_id', $user->prodi_id);
    });
}


  //KAJUR ALL MAHASISWA
  if (in_array('kajur', $user->roles)) {

  }

    return $query->groupBy('student_user_id')
        ->orderByDesc('ipk')
        ->limit(10)
        ->with('student:id,name')
        ->get();
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
    public static function hitungPerwalian(Collection $data): array
    {
        return [
            'totalMahasiswa' => $data->count(),
            'totalPending'   => $data->where('status_perwalian', 'Pending')->count(),
            'totalDone'      => $data->where('status_perwalian', 'Done')->count(),
        ];
    }

}
