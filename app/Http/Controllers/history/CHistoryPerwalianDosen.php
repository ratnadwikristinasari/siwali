<?php

namespace App\Http\Controllers\history;

use App\Helpers\MahasiswaHelper;
use App\Helpers\SessionApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Advise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CHistoryPerwalianDosen extends Controller
{
    public function index(Request $request)
    {
        $token = Auth::user()->token;
        $majorId = Auth::user()->major_id;
        $page = (int) $request->query('page', 1);
        $search = $request->query('search', '');
        $type = $request->input('type');
        $status = $request->input('status');
        $sort_ipk = $request->input('sort_ipk');
        $selectedSessionId = $request->query('session_id', '');
        $selectedSemesterId = $request->query('semester_id', $request->query('semester', ''));

        $user = Auth::user();
        $response = MahasiswaHelper::getMahasiswa(
            $token,
            $majorId,
            $page,
            $search,
            $selectedSemesterId,
            $selectedSemesterId,
        );

        $perwaliandosen = Advise::where('lecture_user_id', $user->id)
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('status', 'like', '%' . $search . '%')
                        ->orWhere('keluhan', 'like', '%' . $search . '%')
                        ->orWhere('masukan', 'like', '%' . $search . '%')
                        ->orWhereHas('student', function ($studentQuery) use ($search) {
                            $studentQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($selectedSemesterId, function ($query, $semester) {
                $query->where('semester_id', $semester);
            })
            ->when($sort_ipk, function ($query, $sort_ipk) {
                 $query->orderBy('ipk', $sort_ipk);
                })
            ->orderBy('status', 'desc')
            ->orderBy('ipk', 'asc')
            ->paginate(10)
            ->withQueryString();

            $sessions = SessionApiHelper::getAsOptions($token);

        return view('content.dataperwalian-dosen', compact('perwaliandosen', 'sessions'));
    }
}
