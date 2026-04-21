<?php

namespace App\Providers;

use App\Models\Advise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Blade::if('role', function (string $roles, string $mode = 'any') {
      if (!Auth::check()) return false;
      return Auth::user()->matchesRoles($roles, $mode);
    });

    Blade::if('notrole', function (string $roles, string $mode = 'any') {
      if (!Auth::check()) return true;
      return !Auth::user()->matchesRoles($roles, $mode);
    });
    View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                // Hitung Perwalian Pending (Tampil di Perwalian Mahasiswa)
                $pendingPerwalianCount = Advise::where('lecture_user_id', $user->id)
                    ->where('status', 'pending')
                    ->count();

                // Hitung Pengesahan KHS (Tampil di Pengesahan KHS)
                // (Sesuaikan filter where-nya jika ada spesifik untuk role/user tertentu)
                $pendingKHSCount = Advise::where('status', 'signed')
                    ->count();

                // Passing variabel ke semua view (bisa diakses di file blade sidebar menu)
                $view->with(compact('pendingPerwalianCount', 'pendingKHSCount'));
            }
        });
    }
}
