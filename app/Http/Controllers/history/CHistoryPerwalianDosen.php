<?php

namespace App\Http\Controllers\history;

use App\Http\Controllers\Controller;
use App\Models\Advise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CHistoryPerwalianDosen extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $user = Auth::user();
        $perwaliandosen = Advise::where('lecture_user_id', $user->id)
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('status', 'like', '%' . $search . '%')
                        ->orWhere('keluhan', 'like', '%' . $search . '%')
                        ->orWhere('masukan', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('status', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('content.dataperwalian-dosen', compact('perwaliandosen'));
    }
}
