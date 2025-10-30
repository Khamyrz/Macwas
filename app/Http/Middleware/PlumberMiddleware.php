<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlumberMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if (!auth()->user()->isPlumber()) {
            abort(403, 'Unauthorized access. Plumber privileges required.');
        }

        return $next($request);
    }
}




