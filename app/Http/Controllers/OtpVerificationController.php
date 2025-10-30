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
    public function show(): View
    {
        return view('auth.otp-verify');
    }

    /**
     * Verify OTP code
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6']
        ]);

        $userId = $request->session()->get('otp_user_id');
        $redirectRoute = $request->session()->get('otp_redirect_route');

        if (!$userId || !$redirectRoute) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $otp = OtpVerification::where('user_id', $userId)
            ->where('otp_code', $request->otp_code)
            ->where('type', 'login')
            ->first();

        if (!$otp || !$otp->isValid()) {
            return back()->withErrors([
                'otp_code' => 'Invalid or expired OTP code.'
            ]);
        }

        // Mark OTP as used
        $otp->update(['is_used' => true]);

        // Get user and login
        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Mark email as verified
        $user->update(['email_verified_at' => now()]);

        // Regenerate session to prevent CSRF issues
        $request->session()->regenerate();

        // Login user
        Auth::login($user);

        // Clear OTP session data
        $request->session()->forget(['otp_user_id', 'otp_redirect_route']);

        // Redirect to the intended route
        return redirect()->intended($redirectRoute)->with('success', 'Email verified successfully!');
    }

    /**
     * Resend OTP
     */
    public function resend(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Generate new OTP
        $otp = OtpVerification::generateOtp($userId, 'login');

        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp->otp_code, 'login'));
            return back()->with('success', 'A new OTP has been sent to your email.');
        } catch (\Exception $e) {
            \Log::error('Failed to resend OTP: ' . $e->getMessage());
            return back()->with('error', 'Failed to send OTP. Please try again.');
        }
    }
}
