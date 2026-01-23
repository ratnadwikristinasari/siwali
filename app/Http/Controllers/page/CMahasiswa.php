<?php

namespace App\Http\Controllers\page;

use App\Helpers\MahasiswaHelper;
use App\Helpers\DashboardHelper;
use App\Http\Controllers\Controller;
use App\Models\Advise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CMahasiswa extends Controller
{
public function index(Request $request)
{
    
    $token = Auth::user()->token;
    $majorId = '019a4723-1d2f-733b-b9ff-25c2e27440c2';
    $page = $request->query('page', 1);

    $response = MahasiswaHelper::getMahasiswa($token, $majorId, $page);
   
    if (!isset($response['data']) || empty($response['data'])) {
        $data = collect();
        $meta = ['total' => 0, 'per_page' => 10, 'page' => 1];
    } else {
        $data = collect($response['data']);
        $meta = $response['meta'] ?? ['total' => count($data), 'per_page' => 10, 'page' => $page];
    }

    if ($data->isNotEmpty()) {

        $studentIds = $data->pluck('student_id')->toArray();
        
        // Cari advises dengan student_id yang cocok
        $advises = Advise::whereIn('student_id', $studentIds)
            ->select('student_id', 'status', 'created_at')
            ->orderBy('status', 'desc')
            ->get()
            ->groupBy('student_id')
            ->map(function ($items) {
                $first = $items->first();
                return $first ? strtolower($first->status) : null;
            });

        // Inject status
        $data = $data->map(function ($mhs) use ($advises) {
            // Gunakan student_id untuk mapping
            $mhs['status_perwalian'] = $advises[$mhs['student_id']] ?? null;
            return $mhs;
        });
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

    return view('content.datamahasiswa', compact('mahasiswas'));
}
}
