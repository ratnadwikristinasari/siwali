<?php

namespace App\Http\Controllers\page;

use App\Helpers\DosenHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CDosen extends Controller
{
  public function index(Request $request)
{
    $token = Auth::user()->token;
    $majorId = '019a4723-1d2f-733b-b9ff-25c2e27440c2';
    //$majorId = $request->query('major_id');
    $page = $request->query('page', 1);

    // Ambil data dari API
    $response = DosenHelper::getdosen($token, $majorId, $page);

    // Ambil data dosen dari position "dosen"
    $data = collect($response['data'])
        ->where('position', 'DOSEN') // filter DOSEN
        ->values();

    // Pagination dari API
    $meta = $response['meta'];

    $dosens = new \Illuminate\Pagination\LengthAwarePaginator(
        $data,                     // data dosen
        $meta['total'],            // total item dari API
        $meta['per_page'],         // item per halaman
        $meta['page'],             // halaman saat ini
        [
            'path' => $request->url(),
            'query' => $request->query(), // menjaga major_id tetep nyambung di pagination
        ]
    );

    return view('content.datadosen', compact('dosens'));
}

}
