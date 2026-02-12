<?php

namespace App\Http\Controllers\page;

use App\Helpers\MahasiswaHelper;
use App\Http\Controllers\Controller;
use App\Models\Advise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CMahasiswa extends Controller
{
    public function index(Request $request)
    {

        $token = Auth::user()->token;
        $majorId = Auth::user()->major_id;
        $page = $request->query('page', 1);
        $search = $request->query('search', '');

        $response = MahasiswaHelper::getMahasiswa($token, $majorId, $page, $search);

        if (!isset($response['data']) || empty($response['data'])) {
            $data = collect();
            $meta = ['total' => 0, 'per_page' => 10, 'page' => 1];
        } else {
            $data = collect($response['data']);
            $meta = $response['meta'] ?? ['total' => count($data), 'per_page' => 10, 'page' => $page];
        }

        if ($data->isNotEmpty()) {
            $studentIds = $data->pluck('student_id')->toArray();

            $advises = Advise::whereIn('student_id', $studentIds)
                ->where('type', 'gpa_advising')
                ->select('student_id', 'status', 'created_at')
                ->orderBy('status', 'desc')
                ->get()
                ->groupBy('student_id')
                ->map(function ($items) {
                    $first = $items->first();
                    return $first ? strtolower($first->status) : null;
                });

            $data = $data->map(function ($mhs) use ($advises) {
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
