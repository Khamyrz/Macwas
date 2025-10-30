<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if (!auth()->user()->isCustomer()) {
            abort(403, 'Unauthorized access. Customer privileges required.');
        }

        return $next($request);
    }
}




