<?php

namespace App\Http\Controllers\page;

use App\Helpers\GlobalHelper;
use App\Helpers\MahasiswaHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CSupervisorAdmin extends Controller
{
    private const POSITIONS = [
        'academic_advisor' => 'Dosen Wali',
        'supervisor_1' => 'Pembimbing 1',
        'supervisor_2' => 'Pembimbing 2',
        'supervisor_internship' => 'Pembimbing Magang',
    ];

    public function index(Request $request)
    {
        $token = Auth::user()->token;

        $response = MahasiswaHelper::getAllStudents($token, [
            'search' => $request->query('search', ''),
            'per_page' => 10,
            'page' => (int) $request->query('page', 1),
        ]);
        $data = $response['data'] ?? [];
        $meta = $response['meta'] ?? ['total' => count($data), 'per_page' => 10, 'page' => 1];

        $students = new LengthAwarePaginator($data, $meta['total'], $meta['per_page'], $meta['page'], [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        $lecturers = GlobalHelper::requestWithToken('/employees/options', $token, 'GET', [], ['position' => 'DOSEN'])
            ->json('data') ?? [];
        $positions = self::POSITIONS;

        return view('content.supervisor-admin', compact('students', 'lecturers', 'positions'));
    }

    public function show(string $studentId)
    {
        abort_unless(Str::isUuid($studentId), 404);
        $response = GlobalHelper::requestWithToken("/supervisor-lectures/student/{$studentId}", Auth::user()->token);

        return response()->json($response->json(), $response->status());
    }

    public function update(Request $request, string $studentId)
    {
        abort_unless(Str::isUuid($studentId), 404);
        $payload = $request->validate(
            collect(array_keys(self::POSITIONS))
                ->mapWithKeys(fn($key) => [$key => ['nullable', 'string', 'uuid']])
                ->all()
        );

        // key hadir tapi null/kosong = hapus ("" ke BE); key absen = tidak berubah
        $payload = collect($request->only(array_keys($payload)))
            ->map(fn($v) => $v ?? '')
            ->all();

        $response = GlobalHelper::requestWithToken("/supervisor-lectures/student/{$studentId}", Auth::user()->token, 'PUT', $payload);

        return response()->json($response->json(), $response->status());
    }
}
