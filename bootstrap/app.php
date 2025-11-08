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
        // Security headers are set via event listener in AppServiceProvider
        // This approach works even if SecurityHeaders.php file doesn't exist on production
        
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
