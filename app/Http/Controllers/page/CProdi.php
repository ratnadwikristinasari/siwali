<?php

namespace App\Http\Controllers\page;

use App\Helpers\MajorHelper;
use App\Helpers\ProdiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CProdi extends Controller
{
    public function getProdiById(Request $request)
    {
        $loggedUser = Auth::user();
        $token = $loggedUser->token;
        $page = $request->query('page', 1);
        $search = $request->query('search', '');

        $majors = [];
        $needMajor = false;
        $majorId = $loggedUser->major_id;
        if ($loggedUser->isSuperadmin()) {
            $majors = MajorHelper::getOptions($token);
            $majorId = $request->query('major_id', '');
            $needMajor = $majorId === '';
        }

        if ($needMajor) {
            $prodis = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['path' => $request->url(), 'query' => $request->query()]);

            return view('content.dataprodi', compact('prodis', 'majors', 'needMajor'));
        }

        $response = ProdiHelper::getprodi($token, $majorId, $page, $search);
        $data = $response['data'];

        $meta = $response['meta'];

        $prodis = new \Illuminate\Pagination\LengthAwarePaginator(
            $data,
            $meta['total'],            // total item dari API
            $meta['per_page'],         // item per halaman
            $meta['page'],             // halaman saat ini
            [
                'path' => $request->url(),
                'query' => $request->query(), // menjaga major_id tetep nyambung di pagination
            ]
        );

        // dd($prodis);

        return view('content.dataprodi', compact('prodis', 'majors', 'needMajor'));
    }
}
