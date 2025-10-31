@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2 cursor-pointer" id="adminUnlock" title="Login">
                Login
            </h2>
            <p class="text-sm text-gray-600">Sign in to your account</p>
            @if(isset($adminExists) && !$adminExists)
                <div class="mt-4">
                    <a href="{{ route('admin.register') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        Create Admin Account
                    </a>
                </div>
            @endif
        </div>


        <!-- Single Login Form -->
        <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="g-recaptcha-response" id="recaptcha_token">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            
            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Login As</label>
                <select id="role" name="role" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select your role</option>
                    <option value="accountant" {{ old('role') == 'accountant' ? 'selected' : '' }}>💼 Accountant</option>
                    <option value="plumber" {{ old('role') == 'plumber' ? 'selected' : '' }}>🛠️ Plumber</option>
                    <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>👤 Customer</option>
                </select>
                <!-- Hidden admin option -->
                <select id="adminRole" name="role" class="hidden">
                    <option value="admin">👑 Admin</option>
                </select>
            </div>
            
            <!-- Email/Customer Number Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    <span id="emailLabel">Email</span>
                    <span id="customerNumberLabel" class="hidden">Customer Number or Email</span>
                </label>
                <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter email or customer number">
                <p id="customerNumberHelp" class="mt-1 text-xs text-gray-500 hidden">
                    You can login using your customer number (YYYY-XXXX) or email address
                </p>
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input id="password" type="password" name="password" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember" 
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-700">
                    Remember me
                </label>
            </div>

            <!-- Submit Button -->
            <button id="submitBtn" type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                Sign In
            </button>
        </form>

        

        <!-- Forgot Password Link -->
        <div class="mt-4 text-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                    Forgot your password?
                </a>
            @endif
        </div>

        <!-- Registration Link -->
        <div class="text-center border-t pt-4 mt-4">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Register here
                </a>
            </p>
        </div>

        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js?render=6LeSvPkrAAAAAFnJ0le2XZXwQDzeUzShhV-LT5ws"></script>
<script>
// Enhanced lockout mechanism variables
let failedAttempts = parseInt(localStorage.getItem('loginFailedAttempts')) || 0;
let lockoutEndTime = parseInt(localStorage.getItem('loginLockoutEndTime')) || 0;
let lockoutTimer = null;
let unlockMessageShown = false;
let currentEmail = '{{ old("email") }}';
let serverLockoutInfo = @json($lockoutInfo ?? null);

// Initialize lockout status on page load
window.addEventListener('load', function() {
    // Check server-side lockout info first
    if (serverLockoutInfo) {
        if (serverLockoutInfo.locked) {
            // Server says we're locked out
            const remainingMs = serverLockoutInfo.remaining_minutes * 60 * 1000;
            lockoutEndTime = Date.now() + remainingMs;
            localStorage.setItem('loginLockoutEndTime', lockoutEndTime.toString());
            startLockoutTimer(remainingMs);
        } else if (serverLockoutInfo.remaining_attempts < 4) {
            // Track remaining attempts (no UI display)
            failedAttempts = 4 - serverLockoutInfo.remaining_attempts;
            localStorage.setItem('loginFailedAttempts', failedAttempts.toString());
        }
    } else {
        // Check client-side lockout status
        checkLockoutStatus();
    }
});

// Check if currently locked out
window.checkLockoutStatus = function() {
    const now = Date.now();
    if (lockoutEndTime > now) {
        // Still locked out
        const remainingTime = lockoutEndTime - now;
        startLockoutTimer(remainingTime);
        return true;
    } else {
        // Lockout expired
        failedAttempts = 0;
        lockoutEndTime = 0;
        localStorage.removeItem('loginFailedAttempts');
        localStorage.removeItem('loginLockoutEndTime');
        return false;
    }
};

// Show remaining attempts (removed - only SweetAlert messages now)
function showRemainingAttempts(attempts) {
    // Function kept for compatibility but no longer shows UI elements
}

// Hide remaining attempts (removed - only SweetAlert messages now)
function hideRemainingAttempts() {
    // Function kept for compatibility but no longer hides UI elements
}

// Start lockout timer with SweetAlert only
function startLockoutTimer(duration) {
    disableForm();
    
    let countdown = Math.floor(duration / 1000); // Convert to seconds
    const totalSeconds = countdown;
    
    const timerInterval = setInterval(() => {
        countdown--;
        
        if (countdown <= 0) {
            clearInterval(timerInterval);
            Swal.close();
            
            // Clean up lockout
            failedAttempts = 0;
            lockoutEndTime = 0;
            unlockMessageShown = false;
            localStorage.removeItem('loginFailedAttempts');
            localStorage.removeItem('loginLockoutEndTime');
            enableForm();
            
            // Show unlock message only once
            if (!unlockMessageShown) {
                unlockMessageShown = true;
                Swal.fire({
                    icon: 'success',
                    title: 'Account Unlocked!',
                    text: 'You can now try logging in again.',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        } else {
            const minutes = Math.floor(countdown / 60);
            const seconds = countdown % 60;
            const progressPercent = (countdown / totalSeconds) * 100;
            
            Swal.update({
                html: `
                    <div style="text-align: center;">
                        <div style="font-size: 48px; margin-bottom: 15px;">🔒</div>
                        <p style="font-size: 20px; margin-bottom: 10px; font-weight: bold; color: #dc2626;">
                            Account Locked!
                        </p>
                        <p style="font-size: 16px; color: #6b7280; margin-bottom: 20px;">
                            Too many failed login attempts
                        </p>
                        <div style="background: #f3f4f6; border-radius: 12px; padding: 20px; margin: 15px 0; border: 2px solid #e5e7eb;">
                            <p style="font-size: 14px; color: #6b7280; margin: 0 0 10px 0;">Account will be unlocked in:</p>
                            <p style="font-size: 32px; font-weight: bold; color: #dc2626; margin: 10px 0;">${minutes}:${seconds.toString().padStart(2, '0')}</p>
                            <p style="font-size: 12px; color: #6b7280; margin: 0;">minutes</p>
                        </div>
                        <div style="background: #e5e7eb; border-radius: 8px; height: 8px; margin: 15px 0;">
                            <div style="background: #dc2626; height: 8px; border-radius: 8px; width: ${progressPercent}%; transition: width 1s ease;"></div>
                        </div>
                    </div>
                `
            });
        }
    }, 1000);
    
    // Initial SweetAlert
    Swal.fire({
        icon: 'error',
        title: 'Account Locked!',
        html: `
            <div style="text-align: center;">
                <div style="font-size: 48px; margin-bottom: 15px;">🔒</div>
                <p style="font-size: 20px; margin-bottom: 10px; font-weight: bold; color: #dc2626;">
                    Account Locked!
                </p>
                <p style="font-size: 16px; color: #6b7280; margin-bottom: 20px;">
                    Too many failed login attempts
                </p>
                <div style="background: #f3f4f6; border-radius: 12px; padding: 20px; margin: 15px 0; border: 2px solid #e5e7eb;">
                    <p style="font-size: 14px; color: #6b7280; margin: 0 0 10px 0;">Account will be unlocked in:</p>
                    <p style="font-size: 32px; font-weight: bold; color: #dc2626; margin: 10px 0;">${Math.floor(countdown / 60)}:${(countdown % 60).toString().padStart(2, '0')}</p>
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">minutes</p>
                </div>
                <div style="background: #e5e7eb; border-radius: 8px; height: 8px; margin: 15px 0;">
                    <div style="background: #dc2626; height: 8px; border-radius: 8px; width: 100%;"></div>
                </div>
            </div>
        `,
        showConfirmButton: false,
        timer: duration,
        timerProgressBar: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
}

// Show lockout status (removed - only SweetAlert messages now)
function showLockoutStatus() {
    // Function kept for compatibility but no longer shows UI elements
}

// Hide lockout status (removed - only SweetAlert messages now)
function hideLockoutStatus() {
    // Function kept for compatibility but no longer hides UI elements
}

// Disable form elements
function disableForm() {
    document.getElementById('role').disabled = true;
    document.getElementById('email').disabled = true;
    document.getElementById('password').disabled = true;
    document.getElementById('remember').disabled = true;
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').textContent = 'Account Locked';
    document.getElementById('submitBtn').classList.add('opacity-50', 'cursor-not-allowed');
}

// Enable form elements
function enableForm() {
    document.getElementById('role').disabled = false;
    document.getElementById('email').disabled = false;
    document.getElementById('password').disabled = false;
    document.getElementById('remember').disabled = false;
    document.getElementById('submitBtn').disabled = false;
    document.getElementById('submitBtn').textContent = 'Sign In';
    document.getElementById('submitBtn').classList.remove('opacity-50', 'cursor-not-allowed');
}

// Enhanced failed login handler
window.handleFailedLogin = function() {
    // Don't increment here - we're syncing with server-side count
    // failedAttempts++; // Removed - handled in session error handler
    localStorage.setItem('loginFailedAttempts', failedAttempts.toString());
    
    if (failedAttempts >= 4) {
        // Trigger lockout
        lockoutEndTime = Date.now() + (5 * 60 * 1000); // 5 minutes
        localStorage.setItem('loginLockoutEndTime', lockoutEndTime.toString());
        
        Swal.fire({
            icon: 'error',
            title: 'Account Locked!',
            text: 'Too many failed login attempts. Your account has been locked for 5 minutes.',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
        
        startLockoutTimer(5 * 60 * 1000);
    } else {
        const remainingAttempts = 4 - failedAttempts;
        
        // Show countdown timer in SweetAlert
        let countdown = 1.5; // 1.5 seconds countdown
        const timerInterval = setInterval(() => {
            countdown -= 0.1;
            if (countdown <= 0) {
                clearInterval(timerInterval);
                Swal.close();
            } else {
                Swal.update({
                    html: `
                        <div style="text-align: center;">
                            <div style="font-size: 48px; margin-bottom: 15px;">⚠️</div>
                            <p style="font-size: 20px; margin-bottom: 10px; font-weight: bold; color: #f59e0b;">
                                Invalid Credentials!
                            </p>
                            <p style="font-size: 16px; color: #6b7280; margin-bottom: 20px;">
                                You have <strong style="color: #dc2626;">${remainingAttempts}</strong> attempt${remainingAttempts > 1 ? 's' : ''} left before lockout
                            </p>
                            <div style="background: #f3f4f6; border-radius: 12px; padding: 20px; margin: 15px 0; border: 2px solid #e5e7eb;">
                                <p style="font-size: 14px; color: #6b7280; margin: 0 0 10px 0;">Next attempt allowed in:</p>
                                <p style="font-size: 32px; font-weight: bold; color: #dc2626; margin: 10px 0;">${countdown.toFixed(1)}</p>
                                <p style="font-size: 12px; color: #6b7280; margin: 0;">seconds</p>
                            </div>
                        </div>
                    `
                });
            }
        }, 100);
        
        Swal.fire({
            icon: 'warning',
            title: 'Login Failed',
            html: `
                <div style="text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 15px;">⚠️</div>
                    <p style="font-size: 20px; margin-bottom: 10px; font-weight: bold; color: #f59e0b;">
                        Invalid Credentials!
                    </p>
                    <p style="font-size: 16px; color: #6b7280; margin-bottom: 20px;">
                        You have <strong style="color: #dc2626;">${remainingAttempts}</strong> attempt${remainingAttempts > 1 ? 's' : ''} left before lockout
                    </p>
                    <div style="background: #f3f4f6; border-radius: 12px; padding: 20px; margin: 15px 0; border: 2px solid #e5e7eb;">
                        <p style="font-size: 14px; color: #6b7280; margin: 0 0 10px 0;">Next attempt allowed in:</p>
                        <p style="font-size: 32px; font-weight: bold; color: #dc2626; margin: 10px 0;">${countdown}</p>
                        <p style="font-size: 12px; color: #6b7280; margin: 0;">seconds</p>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    }
};

// Handle successful login
function handleSuccessfulLogin() {
    failedAttempts = 0;
    lockoutEndTime = 0;
    unlockMessageShown = false;
    localStorage.removeItem('loginFailedAttempts');
    localStorage.removeItem('loginLockoutEndTime');
    if (lockoutTimer) {
        clearInterval(lockoutTimer);
    }
}

// Role change handler
document.getElementById('role').addEventListener('change', function() {
    const role = this.value;
    const emailLabel = document.getElementById('emailLabel');
    const customerNumberLabel = document.getElementById('customerNumberLabel');
    const customerNumberHelp = document.getElementById('customerNumberHelp');
    const emailInput = document.getElementById('email');
    
    if (role === 'customer') {
        emailLabel.classList.add('hidden');
        customerNumberLabel.classList.remove('hidden');
        customerNumberHelp.classList.remove('hidden');
        emailInput.placeholder = 'Enter customer number (YYYY-XXXX) or email';
    } else {
        emailLabel.classList.remove('hidden');
        customerNumberLabel.classList.add('hidden');
        customerNumberHelp.classList.add('hidden');
        emailInput.placeholder = 'Enter email address';
    }
});

// Admin unlock mechanism - click logo 6 times
let adminClickCount = 0;
const adminUnlockTitle = document.getElementById('adminUnlock');

adminUnlockTitle.addEventListener('click', function() {
    adminClickCount++;
    
    if (adminClickCount === 6) {
        // Unlock admin option
        const roleSelect = document.getElementById('role');
        const adminOption = document.createElement('option');
        adminOption.value = 'admin';
        adminOption.textContent = '👑 Admin';
        adminOption.selected = true;
        
        // Insert at the beginning
        roleSelect.insertBefore(adminOption, roleSelect.firstChild.nextSibling);
        
        // Update title
        this.textContent = 'Login';
        this.style.color = '#16a34a';
        
        // Show success message with SweetAlert
        Swal.fire({
            icon: 'success',
            title: 'Admin Unlocked!',
            text: 'Admin login has been unlocked! 🎉',
            confirmButtonText: 'OK'
        });
        
        // Reset counter
        adminClickCount = 0;
    } else if (adminClickCount > 6) {
        adminClickCount = 0;
    }
});

// Check lockout status on page load
window.addEventListener('load', function() {
    checkLockoutStatus();
});

// Submit handler with reCAPTCHA v3
document.getElementById('loginForm').addEventListener('submit', function(e) {
    // Check if locked out
    if (checkLockoutStatus()) {
        e.preventDefault();
        return;
    }

    e.preventDefault();

    // Try to capture precise geolocation before submit
    const submitWithCaptcha = () => {
        grecaptcha.ready(function() {
            grecaptcha.execute('6LeSvPkrAAAAAFnJ0le2XZXwQDzeUzShhV-LT5ws', {action: 'login'})
                .then(function(token) {
                    var tokenInput = document.getElementById('recaptcha_token');
                    if (tokenInput) { tokenInput.value = token; }
                    e.target.submit();
                })
                .catch(function() {
                    Swal.fire({ icon: 'error', title: 'reCAPTCHA Error', text: 'Verification failed. Please try again.', confirmButtonText: 'OK' });
                });
        });
    };

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            latInput.value = pos.coords.latitude.toFixed(7);
            lngInput.value = pos.coords.longitude.toFixed(7);
            submitWithCaptcha();
        }, function() {
            submitWithCaptcha();
        }, { enableHighAccuracy: true, timeout: 3000, maximumAge: 0 });
    } else {
        submitWithCaptcha();
    }
});


// Handle all session messages with SweetAlert
document.addEventListener('DOMContentLoaded', function() {
    // Handle success messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonText: 'OK'
        });
    @endif

    // Handle error messages with enhanced lockout mechanism
    @if(session('error'))
        // Check if this is a lockout-related error
        const errorMessage = '{{ session('error') }}';
        if (errorMessage.includes('locked') || errorMessage.includes('too many failed attempts')) {
            // This is a lockout error - don't trigger handleFailedLogin
            Swal.fire({
                icon: 'error',
                title: 'Account Locked!',
                text: errorMessage,
                confirmButtonText: 'OK',
                timer: 5000,
                timerProgressBar: true
            });
        } else {
            // Regular login error - sync with server-side attempt count
            setTimeout(function() {
                if (typeof handleFailedLogin === 'function') {
                    // Get the actual failed attempts from server-side data
                    const serverAttempts = serverLockoutInfo ? (4 - serverLockoutInfo.remaining_attempts) : 0;
                    failedAttempts = Math.max(failedAttempts, serverAttempts);
                    localStorage.setItem('loginFailedAttempts', failedAttempts.toString());
                    handleFailedLogin();
                } else {
                    // Fallback SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            }, 200);
        }
    @endif

    // Handle info messages
    @if(session('message'))
        Swal.fire({
            icon: 'info',
            title: 'Information',
            text: '{{ session('message') }}',
            confirmButtonText: 'OK'
        });
    @endif

    // Handle validation errors
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: '<ul style="text-align: left;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            confirmButtonText: 'OK'
        });
    @endif

    // Show success message from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    if (success) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: decodeURIComponent(success),
            confirmButtonText: 'OK'
        });
        
        // Remove the success parameter from URL without reloading
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url);
    }
});

</script>
<style>
</style>
@endsection
