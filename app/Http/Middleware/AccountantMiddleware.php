<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if (!auth()->user()->isAccountant()) {
            abort(403, 'Unauthorized access. Accountant privileges required.');
        }

        return $next($request);
    }
}




