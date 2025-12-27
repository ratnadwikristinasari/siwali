<?php

namespace App\Http\Controllers\page;

use App\Helpers\MahasiswaHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CMahasiswa extends Controller
{
  public function index(Request $request)
  {
    $token = Auth::user()->token;
    $majorId = '019a4723-1d2f-733b-b9ff-25c2e27440c2';
    $page = $request->query('page', 1);

    //ambil data dari API
    $response = MahasiswaHelper::getMahasiswa($token, $majorId, $page);

    //Ambil data mahasiswa 
    $data = collect($response['data']);
    $meta = $response['meta'];

    $mahasiswas = new \Illuminate\Pagination\LengthAwarePaginator(
      $data,                     // data mahasiswa
      $meta['total'],            // total item dari API
      $meta['per_page'],         // item per halaman
      $meta['page'],             // halaman saat ini
      [
        'path' => $request->url(),
        'query' => $request->query(), // menjaga major_id tetep nyambung di pagination
      ]
    ); 
    //dd($mahasiswas);
    return view('content.datamahasiswa', compact('mahasiswas'));
  }
}
