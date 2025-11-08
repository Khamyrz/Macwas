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
        // Add security headers to ALL responses
        // This middleware will run on every request
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
        
        // Create a closure middleware that sets security headers
        $securityHeadersMiddleware = function (Request $request, Closure $next) {
            $response = $next($request);
            
            // Set all required security headers
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=()');
            
            return $response;
        };
        
        // Add to global middleware stack
        if (method_exists($kernel, 'pushMiddleware')) {
            $kernel->pushMiddleware($securityHeadersMiddleware);
        }
    }
}
