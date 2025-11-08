<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add security headers middleware to web group (applies to all web routes)
        $securityHeadersPath = __DIR__ . '/../app/Http/Middleware/SecurityHeaders.php';
        if (file_exists($securityHeadersPath)) {
            $middleware->web(append: [\App\Http\Middleware\SecurityHeaders::class]);
        } else {
            // If middleware file doesn't exist, add headers directly via closure
            $middleware->web(append: [function (\Illuminate\Http\Request $request, \Closure $next) {
                $response = $next($request);
                
                // Set all required security headers on every response
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
                $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
                $response->headers->set('X-Content-Type-Options', 'nosniff');
                $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
                $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=()');
                
                return $response;
            }]);
        }
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'accountant' => \App\Http\Middleware\AccountantMiddleware::class,
            'plumber' => \App\Http\Middleware\PlumberMiddleware::class,
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
