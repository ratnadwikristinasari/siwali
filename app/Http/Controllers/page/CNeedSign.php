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

        $advise = Advise::findOrFail($id);
        $advise->update(['status' => 'processing']);

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
        Advise::whereIn('id', $request->ids)->update(['status' => 'processing']);
        foreach ($request->ids as $id) {
            if (is_null($id)) {
                continue; 
            }
            ProcessSignDocument::dispatch($id, $token);
        }

        return redirect()->route('page.need_sign')->with('success', count($request->ids) . ' dokumen perwalian telah ditandatangani.');
    }
}
