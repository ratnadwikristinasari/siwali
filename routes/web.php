<?php

use App\Http\Controllers\Auth\OAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\CLandingpage;
use App\Http\Controllers\dashboard\CDashboard;
use App\Http\Controllers\page\CMahasiswa;
use App\Http\Controllers\page\CDosen;
use App\Http\Controllers\page\CProdi;
use App\Http\Controllers\page\CFormwali;
use App\Http\Controllers\history\CPerwalian;

Route::get('/', [CLandingpage::class, 'index'])->name('content.landingpage');
Route::get('/login', function () {
  redirect()->route('content.landingpage');
  
}) ->name('login');


Route::prefix('auth')->group(function () {
  Route::get('/login', [OAuthController::class, 'redirect'])->name('auth.login');
  Route::get('/callback', [OAuthController::class, 'callback'])->name('auth.callback');
  Route::post('/logout', [OAuthController::class, 'logout'])->name('auth.logout');
});

// Route::get('/', function () {
//    return view('content.orang_tua.dashboard');
// });
//dashboard
Route::middleware('auth')->group(function () {
Route::get('dashboard', [CDashboard::class, 'index'])->name('content.dashboard.dashboard-main');
//page
Route::get('page/mahasiswa', [CMahasiswa::class,'index'])->name('datamahasiswa');
Route::get('page/dosen', [CDosen::class,'index'])->name('datadosen');
Route::get('page/prodi', [CProdi::class,'index'])->name('dataprodi');
Route::get('page/form', [CFormwali::class,'index'])->name('form-perwalian');

//history
Route::get('history/perwalian', [CPerwalian::class,'index'])->name('dataperwalian');

});