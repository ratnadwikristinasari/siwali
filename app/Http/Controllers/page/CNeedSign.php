<?php

namespace App\Http\Controllers\page;


use App\Http\Controllers\Controller;
use App\Jobs\ProcessSignDocument;
use App\Models\Advise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CNeedSign extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $needSigns = Advise::query()
            ->with('student')
            ->where('type', 'gpa_advising')
            ->where('status', 'signed')
            ->when($search, function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('study_program', 'like', '%' . $search . '%');
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('content.need-sign.index', compact('needSigns'));
    }

    public function sign($id)
    {
        $token = Auth::user()->token;

        $claimed = Advise::where('id', $id)
            ->where('type', 'gpa_advising')
            ->where('status', 'signed')
            ->update(['status' => 'processing']);
        abort_unless($claimed, 409, 'Dokumen tidak dalam status signed.');

        ProcessSignDocument::dispatch($id, $token);

        return redirect()->route('page.need_sign')->with('success', 'Dokumen perwalian telah ditandatangani.');
    }

    // Fungsi SignAll
    public function signBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:advise,id',
        ]);
        $token = Auth::user()->token;
        $ids = Advise::whereIn('id', $request->ids)
            ->where('type', 'gpa_advising')
            ->where('status', 'signed')
            ->pluck('id');
        Advise::whereIn('id', $ids)->update(['status' => 'processing']);
        foreach ($ids as $id) {
            ProcessSignDocument::dispatch($id, $token);
        }

        return redirect()->route('page.need_sign')->with('success', count($ids) . ' dokumen perwalian telah ditandatangani.');
    }
}
