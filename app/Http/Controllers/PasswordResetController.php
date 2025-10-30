<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OtpVerification;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    /**
     * Send OTP for password reset
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found in our records.'
            ], 404);
        }

        // Generate OTP for password reset
        $otp = OtpVerification::generateOtp($user->id, 'password_reset');

        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp->otp_code, 'password_reset'));
            
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset OTP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP for password reset
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp_code' => ['required', 'string', 'size:6']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $otp = OtpVerification::where('user_id', $user->id)
            ->where('otp_code', $request->otp_code)
            ->where('type', 'password_reset')
            ->first();

        if (!$otp || !$otp->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP code.'
            ], 400);
        }

        // Mark OTP as used
        $otp->update(['is_used' => true]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.'
        ]);
    }

    /**
     * Reset password after OTP verification
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Check if there's a valid OTP for password reset
        $otp = OtpVerification::where('user_id', $user->id)
            ->where('type', 'password_reset')
            ->where('is_used', true)
            ->where('created_at', '>', now()->subMinutes(10)) // OTP should be used within 10 minutes
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification required. Please verify your OTP first.'
            ], 400);
        }

        // Update password while preserving email verification status
        // If email is already verified, keep it verified
        // If not verified yet, mark it as verified after OTP verification
        $user->update([
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
            'email_verified_at' => $user->email_verified_at ?? now() // Keep existing verification or set to now
        ]);

        // Clear all password reset OTPs for this user
        OtpVerification::where('user_id', $user->id)
            ->where('type', 'password_reset')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.'
        ]);
    }

    /**
     * Resend OTP for password reset
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Generate new OTP
        $otp = OtpVerification::generateOtp($user->id, 'password_reset');

        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp->otp_code, 'password_reset'));
            
            return response()->json([
                'success' => true,
                'message' => 'New OTP sent successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to resend password reset OTP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }
}
