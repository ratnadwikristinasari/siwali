<?php

namespace App\Http\Controllers\page;

use App\Helpers\DosenHelper;
use App\Helpers\ProdiHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CDosen extends Controller
{
    public function index(Request $request)
    {
        $loggedUser = Auth::user();
        $token = $loggedUser->token;
        $page = $request->query('page', 1);
        $search = $request->query('search', '');
        $studyProgramId = $request->query('study_program_id', '');

        if ($loggedUser->hasRole('kajur')) {
            $studyPrograms = ProdiHelper::getprodi($token, $loggedUser->major_id)['data'];
        } else {
            $studyPrograms = [];
            $studyProgramId = $loggedUser->study_program_id;
        }

        $response = DosenHelper::getdosen($token, $loggedUser->major_id, $page, $search, $studyProgramId);
//dd($response);
        $meta = $response['meta'];

        $dosens = new LengthAwarePaginator(
            $response['data'],         // data dosen
            $meta['total'],            // total item dari API
            $meta['per_page'],         // item per halaman
            $meta['page'],             // halaman saat ini
            [
                'path' => $request->url(),
                'query' => $request->query(), // menjaga major_id tetep nyambung di pagination
            ]
        );

        return view('content.datadosen', compact('dosens', 'studyPrograms'));
    }
}
