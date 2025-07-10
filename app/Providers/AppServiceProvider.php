<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
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
        // Rate limiter untuk send otp & verify otp
        RateLimiter::for('otp', function ($request) {
            $clientIp = $request->ip();
            return Limit::perMinutes(5, 20)
                ->by($clientIp)
                ->response(function () {
                    Log::warning('Terlalu banyak percobaan OTP dari IP: ' . request()->ip());
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Akses Anda ditangguhkan karena terlalu banyak percobaan. Silakan coba lagi dalam beberapa menit.'
                    ], 429);
                });
        });
    }
}
