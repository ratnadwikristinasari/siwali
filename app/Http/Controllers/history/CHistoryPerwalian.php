<?php

namespace App\Http\Controllers\history;

use App\Http\Controllers\Controller;
use App\Models\Advise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CHistoryPerwalian extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $status = $request->input('status');
        $user = Auth::user();
        $perwalian = Advise::where('student_user_id', $user->id)
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

        return view('content.dataperwalian', compact('perwalian'));
    }
}
