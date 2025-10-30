<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForceLogoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if we're in development mode and force logout is enabled
        if (app()->environment('local', 'development') && $this->shouldForceLogout()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->with('success', 'All sessions have been cleared. Please log in again.');
        }
        
        return $next($request);
    }
    
    /**
     * Determine if we should force logout users
     */
    private function shouldForceLogout(): bool
    {
        // Check if there's a flag file indicating sessions should be cleared
        $flagFile = storage_path('framework/force-logout.flag');
        
        if (file_exists($flagFile)) {
            // Remove the flag file after checking
            unlink($flagFile);
            return true;
        }
        
        return false;
    }
}
