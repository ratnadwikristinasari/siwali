<?php

namespace App\Providers;

use App\Models\Advise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

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
        // Force HTTPS untuk URL generation di balik reverse proxy Nginx
        // OCTANE_HTTPS=true di-set dari k3s deployment env var
        // APP_ENV staging/production sebagai fallback
        if (config('octane.https') || in_array(config('app.env'), ['staging', 'production'])) {
            URL::forceScheme('https');
        }

        Ratelimiter::for('api', function () {
            return Limit::perMinute(60)->by(optional(request()->user())->id ?: request()->ip());
        });

        RateLimiter::for('oauth-callback', function () {
            return Limit::perMinute(10)->by(optional(request()->user())->id ?: request()->ip());
        });

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
