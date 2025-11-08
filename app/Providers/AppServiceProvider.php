<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        // Add security headers to ALL responses as a global middleware
        // This ensures headers are set even if the SecurityHeaders middleware class doesn't exist
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->pushMiddleware(function (Request $request, $next) {
                $response = $next($request);
                
                // Set all required security headers
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
                $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
                $response->headers->set('X-Content-Type-Options', 'nosniff');
                $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
                $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=()');
                
                return $response;
            });
    }
}
