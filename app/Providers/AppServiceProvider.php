<?php

namespace App\Providers;

use App\Models\Advise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (in_array(config('app.env'), ['staging', 'production'])) {
            URL::forceScheme('https');
        }

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
                static $counts = null;

                if ($counts === null) {
                    $user = Auth::user();

                    $counts = [
                        'pendingPerwalianCount' => Advise::where('lecture_user_id', $user->id)
                            ->where('status', 'pending')
                            ->count(),

                        'pendingKHSCount' => Advise::where('status', 'signed')
                            ->where('type', 'gpa_advising')
                            ->count(),
                    ];
                }

                $view->with($counts);
            }
        });
    }
}
