<?php

use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\CDetailMahasiswa;
use App\Http\Controllers\CSuperappApi;
use App\Http\Controllers\history\CHistoryPerwalianDosen;
use App\Http\Controllers\page\CAllMahasiswa;
use App\Http\Controllers\page\Cbiodata;
use App\Http\Controllers\page\CDetailPerwalian;
use App\Http\Controllers\page\CFormNonKHS;
use App\Http\Controllers\page\CperwalianNonKHS;
use App\Mail\AjukanPerwalianMail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\CLandingpage;
use App\Http\Controllers\dashboard\CDashboard;
use App\Http\Controllers\page\CMahasiswa;
use App\Http\Controllers\page\CDosen;
use App\Http\Controllers\page\CProdi;
use App\Http\Controllers\page\CFormwali;
use App\Http\Controllers\page\CPerwalian;
use App\Http\Controllers\history\CHistoryPerwalian;
use App\Http\Controllers\DropzoneController;

Route::get('/', [CLandingpage::class, 'index'])->name('content.landingpage');
Route::get('/login', [CLandingpage::class, 'index'])->name('login');


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
    Route::get('my/dashboard', [CDashboard::class, 'mydashboard'])->name('content.dashboard.partials.dashboard-dsn');
    Route::get('my/dashboard/data', [CDashboard::class, 'getTopTenStudent'])->name('dashboard.top-ipk');
    Route::get('my/dashboard/analytics', [CDashboard::class, 'getLecturerAnalytics'])->name('dashboard.lecturer-analytics');

    Route::get('biodata', [Cbiodata::class, 'biodata'])->middleware('role:student|orang_tua')->name('biodata');

    Route::prefix('student')->group(function () {
        Route::middleware('role:lecturer')->group(function () {
            Route::get('', [CMahasiswa::class, 'index'])->name('datamahasiswa');
            Route::get('history', [CHistoryPerwalianDosen::class, 'index'])->name('dataperwaliandosen');
            Route::get('preview-gpa/{studentId}/{semesterId}', [CMahasiswa::class, 'previewGPA'])->name('datamahasiswa.preview-gpa');
        });

        Route::middleware('role:kajur|kaprodi')->group(function () {
            Route::get('all', [CAllMahasiswa::class, 'DataMahasiswa'])->name('alldatamahasiswa');
        });
    });

    Route::middleware('role:kajur|kaprodi')->group(function () {
        Route::get('page/dosen', [CDosen::class, 'index'])->name('datadosen');
    });

    Route::prefix('advising')->group(function () {
        Route::middleware('role:student|orang_tua')->group(function () {
            Route::get('', [CFormwali::class, 'index'])->name('form-perwalian');
            Route::post('/', [CPerwalian::class, 'store'])->name('perwalian.store');
            Route::get('history', [CHistoryPerwalian::class, 'index'])->name('dataperwalian');
        });

        Route::middleware('role:lecturer')->group(function () {
            Route::get('/{id}/fill', [CDetailPerwalian::class, 'detail'])->name('perwalian.detail');
            Route::put('/{id}', [CPerwalian::class, 'update'])->name('perwalian.update');
        });
    });

    Route::middleware('role:kajur')->group(function () {
        Route::get('page/prodi', [CProdi::class, 'getProdiById'])->name('dataprodi');
        Route::get('need-sign', [App\Http\Controllers\page\CNeedSign::class, 'index'])->name('page.need_sign');
        Route::post('need-sign/{id}/sign', [App\Http\Controllers\page\CNeedSign::class, 'sign'])->name('page.need_sign.sign');
        Route::post('need-sign/bulk-sign', [App\Http\Controllers\page\CNeedSign::class, 'signBulk'])->name('page.need_sign.sign_bulk');
    });

    Route::middleware('role:kajur|kaprodi|lecturer|sekjur')->group(function () {
        Route::get('detail/mahasiswa/{id}', [CDetailMahasiswa::class, 'index'])->name('detailmahasiswa');
    });

    Route::get('/form-perwalian', [DropzoneController::class, 'index'])->name('dropezone.form');
    Route::post('/upload', [DropzoneController::class, 'khs'])->name('upload.khs');

    Route::get('page/nonkhs', [CFormNonKHS::class, 'index'])->name('form-perwalian-nonkhs');
    Route::post('/perwalian/non', [CperwalianNonKHS::class, 'storekhs'])->name('perwalian.nonkhs');

    Route::get('/perwalian/{id}/edit', [CPerwalian::class, 'edit'])->name('perwalian.edit');

    // Route::get('/perwalian/nonkhs/{id}/edit', [CDetailPerwalianNonKHS::class, 'detail'])->name('perwalian.nonkhs.detail');
    Route::put('/perwalian/nonkhs/{id}', [CperwalianNonKHS::class, 'update'])->name('perwalian.nonkhs.update');
    // Route::get('/perwalian/nonkhs/{id}/detail', [CDetailPerwalianNonKHS::class, 'detail'])->name('perwalian.nonkhs.detail');


    Route::get('/api/semester/option', [CSuperappApi::class, 'semesterOption'])->name('api.semester.option');
});
