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
        // Add security headers middleware (only if file exists)
        $securityHeadersPath = __DIR__ . '/../app/Http/Middleware/SecurityHeaders.php';
        if (file_exists($securityHeadersPath)) {
            $middleware->append('App\Http\Middleware\SecurityHeaders');
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
