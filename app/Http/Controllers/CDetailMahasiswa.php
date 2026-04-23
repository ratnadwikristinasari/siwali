<?php

namespace App\Http\Controllers;

use App\Helpers\MahasiswaHelper;
use App\Models\Advise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class CDetailMahasiswa extends Controller
{
    public function index(Request $request, $id)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $status = $request->input('status');

        $user = Auth::user();
        $userToken = $user->token;

        $student = User::with('parentOf')->where('student_employee_id', $id)->first();

        $raw = MahasiswaHelper::getStudentDetails($userToken, $id);
        $rawData = $raw['data'];
        $semesters = collect($rawData['semesters'] ?? []);
        $lastSemester = $semesters->sortByDesc(fn($s) => $s['year'] * 10 + $s['semester'])->first();

        $userData = [
            'message' => $raw['message'],
            'data' => [
                'name'          => $rawData['user']['name'],
                'email'         => $rawData['user']['email'],
                'status'        => $rawData['user']['status'],
                'gender'        => $rawData['user']['gender']?? null,
                'religion'      => $rawData['user']['religion']?? null,
                'birth_place'   => $rawData['user']['birth_place']?? null,
                'birth_date'    => $rawData['user']['birth_date']?? null,
                'nationality'   => $rawData['user']['nationality']?? null,
                'phone_number'  => null,
                'address'       => null,
                'student_detail' => [
                    'nim'               => $rawData['nim'],
                    'generation'        => $rawData['generation'],
                    'study_program_name' => $rawData['study_program_name'],
                    'major_name'        => $rawData['major_name'],
                    'supervisor_lectures' => $rawData['supervisor_lectures'] ?? [],
                    'student_semester'  => $semesters->map(fn($s) => array_merge($s, [
                        'is_active' => $lastSemester && $s['id'] === $lastSemester['id'],
                    ]))->values()->toArray(),
                ],
            ],
        ];

        if ($student) {
            $perwalian = Advise::where('student_user_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->when($type, function ($query, $type) {
                    $query->where('type', $type);
                })
                ->when($status, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->when($search, function ($query, $search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('status', 'like', '%' . $search . '%')
                            ->orWhere('keluhan', 'like', '%' . $search . '%')
                            ->orWhere('masukan', 'like', '%' . $search . '%');
                    });
                })
                ->paginate(10)
                ->withQueryString();
        } else {
            $perwalian = new LengthAwarePaginator([], 0, 10, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        return view('content.detailmahasiswa', compact('userData', 'perwalian', 'id'));
    }
}
