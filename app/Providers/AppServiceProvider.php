<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Closure;

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
        // Add security headers to ALL responses using global middleware
        // This ensures headers are set even if SecurityHeaders middleware doesn't exist
        $this->app->afterResolving(\Illuminate\Contracts\Http\Kernel::class, function ($kernel) {
            $kernel->pushMiddleware(function (Request $request, Closure $next) {
                $response = $next($request);
                
                // Set all required security headers
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
                $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
                $response->headers->set('X-Content-Type-Options', 'nosniff');
                $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
                $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=()');
                
                return $response;
            });
        });
    }
}
