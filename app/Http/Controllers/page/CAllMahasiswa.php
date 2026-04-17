<?php

namespace App\Http\Controllers\page;

use App\Helpers\MahasiswaHelper;
use App\Helpers\ProdiHelper;
use App\Helpers\SessionApiHelper;
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

        $studyProgramId = $request->query('study_program_id', '');

        if ($user->hasRole('kajur')) {
            $studyPrograms = ProdiHelper::getprodi($token, $user->major_id)['data'];
        } else {
            $studyPrograms = [];
            $studyProgramId = $user->study_program_id;
        }

        $filter = [
            'major_id' => $user->major_id,
            'study_program_id' => $studyProgramId,
            'class' => $request->query('class', ''),
            'semester_id' => $request->query('semester_id', $request->query('semester', '')),
        ];

        $queryParams = [
            'search' => $request->query('search', ''),
            'sort' => $request->query('sort', ''),
            'per_page' => (int) $request->query('per_page', 10),
            'page' => (int) $request->query('page', 1),
            'last_page' => (int) $request->query('last_page', 1),
            'filter' => json_encode($filter),
        ];

        $response = MahasiswaHelper::getAllStudents($token, $queryParams);
        $data = collect($response['data'] ?? []);
        $meta = $response['meta'] ?? [
            'total' => count($data),
            'per_page' => $queryParams['per_page'],
            'page' => $queryParams['page'],
        ];

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

        $sessions = SessionApiHelper::getAsOptions($token);

        return view('content.data-allmahasiswa', compact('mahasiswaall', 'studyPrograms', 'sessions'));
    }
}
