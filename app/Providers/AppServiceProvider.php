<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

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
        RateLimiter::for('login', function (Request $request) {
            return [Limit::perMinute(5)->by($request->input('email').$request->ip())];
        });

        RateLimiter::for('transactions', function (Request $request) {
            $key = optional($request->user())->id ?: $request->ip();
            return [Limit::perMinute(10)->by($key)];
        });

        RateLimiter::for('exports', function (Request $request) {
            $key = optional($request->user())->id ?: $request->ip();
            return [Limit::perMinutes(5,3)->by($key)];
        });
    }
}
