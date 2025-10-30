@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col items-center pt-6 bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="w-full max-w-2xl px-4">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Create Account</h2>
            <p class="text-sm text-gray-600 mt-1">Join our water management system</p>
        </div>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg overflow-hidden">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <!-- General Information -->
                <div>
                    <h3 class="text-base font-semibold text-gray-900 mb-4">General Information</h3>
                    
                    <div class="space-y-4">
                        <!-- Role Selection -->
                        <div>
                            <label for="role" class="block text-xs font-medium text-gray-700 mb-1">Position *</label>
                            <select id="role" name="role" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select position</option>
                                <option value="plumber">Plumber</option>
                                <option value="accountant">Accountant</option>
                              
                            </select>
                            <p class="mt-1 text-xs text-gray-500">All applications require admin approval before login access</p>
                            @error('role')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-xs font-medium text-gray-700 mb-1">First Name *</label>
                            <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            @error('first_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-xs font-medium text-gray-700 mb-1">Last Name *</label>
                            <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            @error('last_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Age -->
                        <div>
                            <label for="age" class="block text-xs font-medium text-gray-700 mb-1">Age *</label>
                            <input id="age" type="number" name="age" value="{{ old('age') }}" min="18" max="120" required 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            @error('age')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-xs font-medium text-gray-700 mb-1">Password *</label>
                            <input id="password" type="password" name="password" required 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-medium text-gray-700 mb-1">Confirm Password *</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- National ID Upload -->
                        <div>
                            <label for="national_id" class="block text-xs font-medium text-gray-700 mb-1">National ID *</label>
                            <input id="national_id" type="file" name="national_id" accept="image/*" required 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Upload image (Max: 2MB)</p>
                            @error('national_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Details -->
                <div>
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Contact Details</h3>
                    
                    <div class="space-y-4">
                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-xs font-medium text-gray-700 mb-1">Address *</label>
                            <input id="address" type="text" name="address" value="{{ old('address') }}" required 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            @error('address')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-xs font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}" 
                                   required maxlength="11" pattern="[0-9]{11}" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Must be exactly 11 digits</p>
                            @error('phone_number')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-xs font-medium text-gray-700 mb-1">Your Email *</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo -->
                        <div>
                            <label for="photo" class="block text-xs font-medium text-gray-700 mb-1">Profile Photo</label>
                            <input id="photo" type="file" name="photo" accept="image/*" 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">Accepted formats: JPEG, PNG, JPG (Max: 2MB)</p>
                            @error('photo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="flex items-start mt-4">
                            <input id="terms" type="checkbox" required 
                                   class="h-4 w-4 mt-0.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="terms" class="ml-2 block text-xs text-gray-700">
                                I do accept the <a href="#" class="underline hover:text-blue-500">Terms and Conditions</a> of your site.
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="px-6 pb-6 text-center">
                <button type="submit" 
                        class="bg-blue-600 text-white font-semibold px-8 py-2.5 text-sm rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                    Register Badge
                </button>
            </div>

            <!-- Login Link -->
            <div class="text-center pb-6">
                <p class="text-xs text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
// Password confirmation validation
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('password_confirmation');
    if (confirmPassword.value) {
        if (this.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
});

// Phone number validation - only allow numbers and limit to 11 digits
document.getElementById('phone_number').addEventListener('input', function(e) {
    // Remove any non-digit characters
    this.value = this.value.replace(/\D/g, '');
    
    // Limit to 11 digits
    if (this.value.length > 11) {
        this.value = this.value.slice(0, 11);
    }
    
    // Set custom validity message
    if (this.value.length > 0 && this.value.length !== 11) {
        this.setCustomValidity('Phone number must be exactly 11 digits');
    } else {
        this.setCustomValidity('');
    }
});

// File size validation for National ID
document.getElementById('national_id').addEventListener('change', function() {
    const file = this.files[0];
    if (file && file.size > 2 * 1024 * 1024) { // 2MB
        alert('File size must be less than 2MB');
        this.value = '';
    }
});

// File size validation for Photo
document.getElementById('photo').addEventListener('change', function() {
    const file = this.files[0];
    if (file && file.size > 2 * 1024 * 1024) { // 2MB
        alert('File size must be less than 2MB');
        this.value = '';
    }
});
</script>
@endsection