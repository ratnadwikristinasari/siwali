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
        if ($appUrl = config('app.url')) {
            URL::forceRootUrl($appUrl);
            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
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
                $user = Auth::user();

                $view->with([
                    'pendingPerwalianCount' => Advise::where('lecture_user_id', $user->id)
                        ->where('status', 'pending')
                        ->count(),

                    'pendingKHSCount' => Advise::where('status', 'signed')
                        ->where('type', 'gpa_advising')
                        ->count(),
                ]);
            }
        });
    }
}
