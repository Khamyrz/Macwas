<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

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
        // Method 1: Add security headers using event listener (most reliable)
        Event::listen(KernelEvents::RESPONSE, function (ResponseEvent $event) {
            if (!$event->isMainRequest()) {
                return;
            }
            
            $response = $event->getResponse();
            
            // Set all required security headers on every response
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=()');
        });
        
        // Method 2: Add security headers using global middleware (backup)
        $this->app->afterResolving(\Illuminate\Contracts\Http\Kernel::class, function ($kernel) {
            if (method_exists($kernel, 'pushMiddleware')) {
                $kernel->pushMiddleware(function (Request $request, Closure $next) {
                    $response = $next($request);
                    
                    // Set all required security headers on every response
                    if ($response && method_exists($response, 'headers')) {
                        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
                        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
                        $response->headers->set('X-Content-Type-Options', 'nosniff');
                        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
                        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=()');
                    }
                    
                    return $response;
                });
            }
        });
    }
}
