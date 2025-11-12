<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OtpVerification;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OtpVerificationController extends Controller
{
    /**
     * Show OTP verification form
     */
    public function show(): View|RedirectResponse
    {
        $userId = session()->get('otp_user_id');
        
        // If there's a user ID in session, check if they're already verified
        if ($userId) {
            $user = User::find($userId);
            if ($user && $user->email_verified_at) {
                // User is already verified, redirect to their dashboard
                $redirectRoute = session()->get('otp_redirect_route', route('dashboard'));
                
                // Clear OTP session data
                session()->forget(['otp_user_id', 'otp_redirect_route', 'otp_required']);
                
                // Login user if not already logged in
                if (!Auth::check() || Auth::id() != $user->id) {
                    Auth::login($user);
                }
                
                return redirect($redirectRoute)->with('success', 'You are already verified.');
            }
        }
        
        // If no user ID in session or user is not verified, show OTP form
        // But also check if user is authenticated and already verified
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->email_verified_at) {
                // User is authenticated and verified, redirect to dashboard
                $redirectRoute = $this->getRedirectRoute($user->role);
                return redirect($redirectRoute);
            }
        }
        
        return view('auth.otp-verify');
    }
    
    /**
     * Get redirect route based on user role
     */
    private function getRedirectRoute(string $role): string
    {
        switch($role) {
            case 'admin':
                return route('admin.dashboard');
            case 'accountant':
                return route('accountant.dashboard');
            case 'plumber':
                return route('plumber.dashboard');
            case 'customer':
                return route('customer.dashboard');
            default:
                return route('dashboard');
        }
    }

    /**
     * Verify OTP code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6']
        ]);

        $userId = $request->session()->get('otp_user_id');
        $redirectRoute = $request->session()->get('otp_redirect_route');

        if (!$userId || !$redirectRoute) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Session expired. Please login again.'], 400);
            }
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $otp = OtpVerification::where('user_id', $userId)
            ->where('otp_code', $request->otp_code)
            ->where('type', 'login')
            ->where('is_used', false)
            ->first();

        if (!$otp || !$otp->isValid()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Invalid or expired OTP code.'], 400);
            }
            return back()->withErrors([
                'otp_code' => 'Invalid or expired OTP code.'
            ]);
        }

        // Mark OTP as used
        $otp->update(['is_used' => true]);

        // Get user
        $user = User::find($userId);
        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Mark email as verified (if not already)
        if (!$user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);
        }

        // Regenerate session to prevent CSRF issues
        $request->session()->regenerate();

        // Login user (if not already logged in)
        if (!Auth::check() || Auth::id() != $user->id) {
            Auth::login($user);
        }

        // Ensure redirect route is set based on user role if not in session
        if (!$redirectRoute) {
            $redirectRoute = $this->getRedirectRoute($user->role);
        }

        // Clear OTP session data
        $request->session()->forget(['otp_user_id', 'otp_redirect_route', 'otp_required']);

        // For AJAX requests (modal), return JSON with redirect URL
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully! Redirecting to dashboard...',
                'redirect' => $redirectRoute
            ]);
        }

        // Redirect to the intended route
        return redirect($redirectRoute)->with('success', 'OTP verified successfully!');
    }

    /**
     * Resend OTP
     */
    public function resend(Request $request)
    {
        $userId = $request->session()->get('otp_user_id');

        if (!$userId) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Session expired. Please login again.'], 400);
            }
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = User::find($userId);
        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Generate new OTP
        $otp = OtpVerification::generateOtp($userId, 'login');

        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp->otp_code, 'login'));
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'A new OTP has been sent to your email.']);
            }
            return back()->with('success', 'A new OTP has been sent to your email.');
        } catch (\Exception $e) {
            \Log::error('Failed to resend OTP: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again.'], 500);
            }
            return back()->with('error', 'Failed to send OTP. Please try again.');
        }
    }
}
