<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;

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
        // Add security headers to ALL responses using event listener
        // This works as a fallback if SecurityHeaders middleware doesn't exist
        Event::listen(RequestHandled::class, function (RequestHandled $event) {
            $response = $event->response;
            
            if ($response && method_exists($response, 'headers')) {
                // Set all required security headers
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
                $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
                $response->headers->set('X-Content-Type-Options', 'nosniff');
                $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
                $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=()');
            }
        });
    }
}
