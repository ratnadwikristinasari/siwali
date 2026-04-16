<?php

namespace App\Http\Controllers\page;

use App\Helpers\MahasiswaHelper;
use App\Http\Controllers\Controller;
use App\Models\Advise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class CAllMahasiswa extends Controller
{
    public function DataMahasiswa(Request $request)
    {
        $user = Auth::user();
   
        $token = $user->token;

        $page = $request->query('page', 1);

        $prodiId = null;
        $majorId = null;

        $userRoles = is_array($user->roles) ? json_encode($user->roles) : $user->roles;
//dd($userRoles);
        if (str_contains($userRoles, 'kajur')) {
            $majorId = $user->major_id;
        } elseif (str_contains($userRoles, 'kaprodi')) {
            $prodiId = $user->study_program_id; 
        }
//dd($majorId);
        $response = MahasiswaHelper::getMahasiswaByProdi($token, $prodiId, $page, $majorId);
//dd($response);

        if (!isset($response['data']) || empty($response['data'])) {
            $data = collect();
            $meta = ['total' => 0, 'per_page' => 10, 'page' => 1];
        } else {
            $data = collect($response['data']);
            $meta = $response['meta'] ?? ['total' => count($data), 'per_page' => 10, 'page' => $page];
        }

        //Status perwalian
        if ($data->isNotEmpty()) {
            $apiStudentKey = 'id'; 
            $studentIds = $data->pluck($apiStudentKey)->toArray(); 

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

            $data = $data->map(function ($mhs) use ($advises, $apiStudentKey) {
                $mhs['status_perwalian'] = $advises[$mhs[$apiStudentKey]] ?? null;
                return $mhs;
            });
            
        }

        $mahasiswaall = new LengthAwarePaginator(
            $data,
            $meta['total'] ?? 0,
            $meta['per_page'] ?? 10,
            $meta['page'] ?? 1,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('content.data-allmahasiswa', compact('mahasiswaall'));
    }
}