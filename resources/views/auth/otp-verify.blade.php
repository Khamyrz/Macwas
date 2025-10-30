@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Verify Your Email</h2>
            <p class="text-sm text-gray-600">Enter the 6-digit code sent to your email</p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}" class="space-y-4" id="otpForm">
            @csrf
            
            <!-- OTP Code Field -->
            <div>
                <label for="otp_code" class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
                <input id="otp_code" type="text" name="otp_code" required autofocus maxlength="6"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-center text-2xl tracking-widest"
                       placeholder="000000" value="{{ old('otp_code') }}">
                @error('otp_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="submitText">Verify Code</span>
                <span id="loadingText" class="hidden">Verifying...</span>
            </button>
        </form>

        <!-- Resend OTP -->
        <div class="mt-4 text-center">
            <form method="POST" action="{{ route('otp.resend') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-500">
                    Didn't receive the code? Resend
                </button>
            </form>
        </div>

        <!-- Back to Login -->
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-500">
                ← Back to Login
            </a>
        </div>
    </div>
</div>

<script>
// Auto-format OTP input
document.getElementById('otp_code').addEventListener('input', function(e) {
    // Remove any non-numeric characters
    this.value = this.value.replace(/[^0-9]/g, '');
    
    // Limit to 6 digits
    if (this.value.length > 6) {
        this.value = this.value.slice(0, 6);
    }
});

// Auto-submit when 6 digits are entered
document.getElementById('otp_code').addEventListener('input', function(e) {
    if (this.value.length === 6) {
        // Small delay to show the complete code
        setTimeout(() => {
            submitForm();
        }, 500);
    }
});

// Handle form submission
function submitForm() {
    const form = document.getElementById('otpForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingText = document.getElementById('loadingText');
    
    // Prevent double submission
    if (submitBtn.disabled) return;
    
    // Show loading state
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    loadingText.classList.remove('hidden');
    
    // Submit form
    form.submit();
}

// Handle form submission on button click
document.getElementById('otpForm').addEventListener('submit', function(e) {
    submitForm();
});

// Handle page refresh to prevent CSRF issues
window.addEventListener('beforeunload', function() {
    // Clear any pending form submissions
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = false;
    }
});
</script>
@endsection

