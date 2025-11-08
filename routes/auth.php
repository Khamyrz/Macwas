<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use Illuminate\Support\Facades\Route;

// Login GET route - allow authenticated users with OTP required to access
Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // OTP Verification Routes (for guest users)
    Route::get('otp-verify', [App\Http\Controllers\OtpVerificationController::class, 'show'])
                ->name('otp.verify');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [App\Http\Controllers\PasswordResetController::class, 'sendOtp'])
                ->name('password.email');

    // OTP-based password reset routes
    Route::post('password/verify-otp', [App\Http\Controllers\PasswordResetController::class, 'verifyOtp'])
                ->name('password.verify-otp');

    Route::post('password/reset-otp', [App\Http\Controllers\PasswordResetController::class, 'resetPassword'])
                ->name('password.reset-otp');

    Route::post('password/resend-otp', [App\Http\Controllers\PasswordResetController::class, 'resendOtp'])
                ->name('password.resend-otp');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.update');
});

Route::middleware('auth')->group(function () {
    // OTP Verification Routes (for authenticated users - modal)
    Route::post('otp-verify', [App\Http\Controllers\OtpVerificationController::class, 'verify'])
                ->name('otp.verify');

    Route::post('otp-resend', [App\Http\Controllers\OtpVerificationController::class, 'resend'])
                ->name('otp.resend');

    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});




