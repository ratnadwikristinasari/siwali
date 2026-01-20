<?php

namespace App\Http\Controllers\page;

use App\Helpers\MahasiswaHelper;
use App\Helpers\DashboardHelper;
use App\Http\Controllers\Controller;
use App\Models\Advise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CMahasiswa extends Controller
{
public function index(Request $request)
{
    $token = Auth::user()->token;
    $majorId = '019a4723-1d2f-733b-b9ff-25c2e27440c2';
    $page = $request->query('page', 1);

    // Ambil data dari API
    $response = MahasiswaHelper::getMahasiswa($token, $majorId, $page);
    
    // Validasi response
    if (!isset($response['data']) || empty($response['data'])) {
        $data = collect();
        $meta = ['total' => 0, 'per_page' => 10, 'page' => 1];
    } else {
        $data = collect($response['data']);
        $meta = $response['meta'] ?? ['total' => count($data), 'per_page' => 10, 'page' => $page];
    }

    // Jika ada data, ambil status perwalian
    if ($data->isNotEmpty()) {
        $nims = $data->pluck('nim')->toArray();
        
        $statusPerwalian = Advise::whereIn('student_user_id', $nims)
            ->select('student_user_id', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('student_user_id')
            ->map(fn($items) => $items->first()->status);

            //dd($statusPerwalian);

        // Inject status ke data mahasiswa
        $data = $data->map(function ($mhs) use ($statusPerwalian) {
            $mhs['status_perwalian'] = $statusPerwalian[$mhs['nim']] ?? null;
            return $mhs; // JANGAN LUPA RETURN
        });
        $totalperwalian = DashboardHelper::hitungPerwalian($data);


    }

    $mahasiswas = new \Illuminate\Pagination\LengthAwarePaginator(
        $data,
        $meta['total'] ?? 0,
        $meta['per_page'] ?? 10,
        $meta['page'] ?? 1,
        [
            'path' => $request->url(),
            'query' => $request->query(),
        ]
    );

    // dd($data);

    return view('content.datamahasiswa', compact('mahasiswas'));
}
}
