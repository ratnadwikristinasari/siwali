<?php

namespace App\Http\Controllers\page;

use App\Helpers\MajorHelper;
use App\Helpers\ProdiHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CProdi extends Controller
{
  public function index(Request $request)
  {
        $token = Auth::user()->token;
        $majorId = '019a4723-1d2f-733b-b9ff-25c2e27440c2';
        $page = $request->query('page', 1);
        //ambil data API
        $response = ProdiHelper::getprodi($token, $majorId, $page);
        $data = $response['data'];

        //pagination
        $meta = $response['meta'];

        $prodis = new \Illuminate\Pagination\LengthAwarePaginator(
        $data,                     // data dosen
        $meta['total'],            // total item dari API
        $meta['per_page'],         // item per halaman
        $meta['page'],             // halaman saat ini
        [
            'path' => $request->url(),
            'query' => $request->query(), // menjaga major_id tetep nyambung di pagination
        ]
    );
        //$prodis = ProdiHelper::getprodi($token );
        //dd($prodis);
    return view('content.dataprodi', compact('prodis'));
  }
}

