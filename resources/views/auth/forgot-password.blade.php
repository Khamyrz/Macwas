@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-blue-600">Reset Password</h2>
            <p class="text-sm text-gray-600 mt-2">Enter your email to receive an OTP</p>
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4" id="forgotPasswordForm">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" id="sendOtpBtn"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="sendOtpText">Send Password OTP</span>
                <span id="sendingText" class="hidden">Sending...</span>
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-500">
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Password Reset Modal -->
<div id="passwordResetModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Set new Password</h3>
                <button onclick="closePasswordResetModal()" id="modalCloseBtn" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- OTP Verification Step -->
            <div id="otpStep" class="space-y-4">
                <div class="text-center mb-4">
                    <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600">Enter the 6-digit OTP sent to your email</p>
                </div>

                <form id="otpForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="otp_code" class="block text-sm font-medium text-gray-700 mb-1">Verification Code</label>
                        <input id="otp_code" type="text" name="otp_code" required maxlength="6"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-center text-2xl tracking-widest"
                               placeholder="000000">
                        <div id="otpError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <button type="submit" id="verifyOtpBtn"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="verifyOtpText">Verify OTP</span>
                        <span id="verifyingText" class="hidden">Verifying...</span>
                    </button>
                </form>

                <div class="text-center">
                    <button onclick="resendOtp()" id="resendOtpBtn" class="text-sm text-blue-600 hover:text-blue-500">
                        Resend OTP
                    </button>
                </div>
            </div>

            <!-- New Password Step -->
            <div id="newPasswordStep" class="space-y-4 hidden">
                <div class="text-center mb-4">
                    <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600">Create your new password</p>
                    <p class="text-xs text-yellow-600 mt-2 font-medium">⚠️ Please complete password reset before closing</p>
                </div>

                <form id="newPasswordForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input id="password" type="password" name="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <div id="passwordError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <div id="passwordConfirmationError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <button type="submit" id="resetPasswordBtn"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="resetPasswordText">Reset Password</span>
                        <span id="resettingText" class="hidden">Resetting...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let userEmail = '';
let otpVerified = false;
let modalLocked = false;

// Handle forgot password form submission
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    userEmail = email;
    
    // Show loading state
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const sendOtpText = document.getElementById('sendOtpText');
    const sendingText = document.getElementById('sendingText');
    
    sendOtpBtn.disabled = true;
    sendOtpText.classList.add('hidden');
    sendingText.classList.remove('hidden');
    
    // Send OTP request
    fetch('{{ route("password.email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: 'email=' + encodeURIComponent(email)
    })
    .then(response => response.text())
    .then(data => {
        // Reset button state
        sendOtpBtn.disabled = false;
        sendOtpText.classList.remove('hidden');
        sendingText.classList.add('hidden');
        
        // Lock modal immediately when opened
        modalLocked = true;
        
        // Hide close button from the start
        document.getElementById('modalCloseBtn').style.display = 'none';
        
        // Show modal
        document.getElementById('passwordResetModal').classList.remove('hidden');
        document.getElementById('otpStep').classList.remove('hidden');
        document.getElementById('newPasswordStep').classList.add('hidden');
        
        // Focus on OTP input
        setTimeout(() => {
            document.getElementById('otp_code').focus();
        }, 100);
    })
    .catch(error => {
        console.error('Error:', error);
        sendOtpBtn.disabled = false;
        sendOtpText.classList.remove('hidden');
        sendingText.classList.add('hidden');
        alert('Error sending OTP. Please try again.');
    });
});

// Handle OTP form submission
document.getElementById('otpForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const otpCode = document.getElementById('otp_code').value;
    
    // Show loading state
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const verifyOtpText = document.getElementById('verifyOtpText');
    const verifyingText = document.getElementById('verifyingText');
    
    verifyOtpBtn.disabled = true;
    verifyOtpText.classList.add('hidden');
    verifyingText.classList.remove('hidden');
    
    // Verify OTP
    fetch('{{ route("password.verify-otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: 'email=' + encodeURIComponent(userEmail) + '&otp_code=' + encodeURIComponent(otpCode)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            otpVerified = true;
            modalLocked = true; // Lock the modal from being closed
            
            // Hide close button
            document.getElementById('modalCloseBtn').style.display = 'none';
            
            // Show new password step
            document.getElementById('otpStep').classList.add('hidden');
            document.getElementById('newPasswordStep').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Set New Password';
            
            // Focus on password input
            setTimeout(() => {
                document.getElementById('password').focus();
            }, 100);
        } else {
            // Show error
            document.getElementById('otpError').textContent = data.message || 'Invalid OTP code';
            document.getElementById('otpError').classList.remove('hidden');
        }
        
        // Reset button state
        verifyOtpBtn.disabled = false;
        verifyOtpText.classList.remove('hidden');
        verifyingText.classList.add('hidden');
    })
    .catch(error => {
        console.error('Error:', error);
        verifyOtpBtn.disabled = false;
        verifyOtpText.classList.remove('hidden');
        verifyingText.classList.add('hidden');
        document.getElementById('otpError').textContent = 'Error verifying OTP. Please try again.';
        document.getElementById('otpError').classList.remove('hidden');
    });
});

// Handle new password form submission
document.getElementById('newPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const password = document.getElementById('password').value;
    const passwordConfirmation = document.getElementById('password_confirmation').value;
    
    // Validate passwords match
    if (password !== passwordConfirmation) {
        document.getElementById('password').classList.add('border-red-500', 'ring-red-500');
        document.getElementById('password_confirmation').classList.add('border-red-500', 'ring-red-500');
        document.getElementById('passwordConfirmationError').textContent = 'Passwords do not match';
        document.getElementById('passwordConfirmationError').classList.remove('hidden');
        return;
    }
    
    // Show loading state
    const resetPasswordBtn = document.getElementById('resetPasswordBtn');
    const resetPasswordText = document.getElementById('resetPasswordText');
    const resettingText = document.getElementById('resettingText');
    
    resetPasswordBtn.disabled = true;
    resetPasswordText.classList.add('hidden');
    resettingText.classList.remove('hidden');
    
    // Reset password
    fetch('{{ route("password.reset-otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: 'email=' + encodeURIComponent(userEmail) + '&password=' + encodeURIComponent(password) + '&password_confirmation=' + encodeURIComponent(passwordConfirmation)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Unlock modal and close
            modalLocked = false;
            closePasswordResetModal();
            window.location.href = '{{ route("login") }}?success=' + encodeURIComponent('Password reset successfully! You can now login with your new password.');
        } else {
            // Show error
            document.getElementById('passwordError').textContent = data.message || 'Error resetting password';
            document.getElementById('passwordError').classList.remove('hidden');
        }
        
        // Reset button state
        resetPasswordBtn.disabled = false;
        resetPasswordText.classList.remove('hidden');
        resettingText.classList.add('hidden');
    })
    .catch(error => {
        console.error('Error:', error);
        resetPasswordBtn.disabled = false;
        resetPasswordText.classList.remove('hidden');
        resettingText.classList.add('hidden');
        document.getElementById('passwordError').textContent = 'Error resetting password. Please try again.';
        document.getElementById('passwordError').classList.remove('hidden');
    });
});

// Password matching validation
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const passwordConfirmation = this.value;
    
    if (passwordConfirmation.length > 0) {
        if (password === passwordConfirmation) {
            this.classList.remove('border-red-500', 'ring-red-500');
            this.classList.add('border-green-500', 'ring-green-500');
            document.getElementById('password').classList.remove('border-red-500', 'ring-red-500');
            document.getElementById('password').classList.add('border-green-500', 'ring-green-500');
            document.getElementById('passwordConfirmationError').classList.add('hidden');
        } else {
            this.classList.remove('border-green-500', 'ring-green-500');
            this.classList.add('border-red-500', 'ring-red-500');
            document.getElementById('password').classList.remove('border-green-500', 'ring-green-500');
            document.getElementById('password').classList.add('border-red-500', 'ring-red-500');
            document.getElementById('passwordConfirmationError').textContent = 'Passwords do not match';
            document.getElementById('passwordConfirmationError').classList.remove('hidden');
        }
    }
});

// OTP input formatting
document.getElementById('otp_code').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 6) {
        this.value = this.value.slice(0, 6);
    }
});

// Resend OTP
function resendOtp() {
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    resendOtpBtn.disabled = true;
    resendOtpBtn.textContent = 'Sending...';
    
    fetch('{{ route("password.resend-otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: 'email=' + encodeURIComponent(userEmail)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('New OTP sent to your email');
        } else {
            alert('Error sending OTP. Please try again.');
        }
        resendOtpBtn.disabled = false;
        resendOtpBtn.textContent = 'Resend OTP';
    })
    .catch(error => {
        console.error('Error:', error);
        resendOtpBtn.disabled = false;
        resendOtpBtn.textContent = 'Resend OTP';
        alert('Error sending OTP. Please try again.');
    });
}

// Close modal
function closePasswordResetModal() {
    // Prevent closing if modal is locked (on password reset step)
    if (modalLocked) {
        alert('Please complete the password reset process before closing.');
        return;
    }
    
    document.getElementById('passwordResetModal').classList.add('hidden');
    // Reset forms
    document.getElementById('otpForm').reset();
    document.getElementById('newPasswordForm').reset();
    // Reset visual states
    document.getElementById('password').classList.remove('border-red-500', 'ring-red-500', 'border-green-500', 'ring-green-500');
    document.getElementById('password_confirmation').classList.remove('border-red-500', 'ring-red-500', 'border-green-500', 'ring-green-500');
    document.getElementById('passwordConfirmationError').classList.add('hidden');
    document.getElementById('otpError').classList.add('hidden');
    document.getElementById('passwordError').classList.add('hidden');
    
    // Reset modal state
    otpVerified = false;
    modalLocked = false;
    document.getElementById('modalCloseBtn').style.display = 'block';
}

// Close modal when clicking outside - but check if locked
document.getElementById('passwordResetModal').addEventListener('click', function(e) {
    if (e.target === this && !modalLocked) {
        closePasswordResetModal();
    } else if (e.target === this && modalLocked) {
        e.stopPropagation();
        e.preventDefault();
        alert('Please complete the password reset process before closing.');
    }
});

// Prevent clicks inside modal from closing it
document.querySelector('#passwordResetModal > div').addEventListener('click', function(e) {
    e.stopPropagation();
});

// Prevent ESC key from closing modal when locked
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modalLocked) {
        e.preventDefault();
        alert('Please complete the password reset process before closing.');
    }
});

// Show success message on login page
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    if (success) {
        alert(decodeURIComponent(success));
    }
});
</script>
@endsection