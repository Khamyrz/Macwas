@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
	<h1 class="text-2xl font-bold mb-4">Create User</h1>
	<form method="POST" action="{{ route('admin.store-user') }}" class="space-y-4">
		@csrf
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
			<select name="role" id="role" required class="w-full border border-gray-300 px-3 py-1.5 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="togglePlumberSelection()">
				<option value="customer">Customer</option>
				<option value="plumber">Plumber</option>
				<option value="accountant">Accountant</option>
			</select>
		</div>
		
		<!-- Plumber Assignment (only shown for customers) -->
		<div id="plumber-assignment" style="display: none;">
			<label class="block text-sm font-medium text-gray-700 mb-1">Assign Plumber</label>
			<select name="assigned_plumber_id" class="w-full border border-gray-300 px-3 py-1.5 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('assigned_plumber_id') border-red-500 @enderror">
				<option value="">Select a plumber (optional)</option>
				@foreach(\App\Models\User::where('role', 'plumber')->where('status', 'active')->get() as $plumber)
					<option value="{{ $plumber->id }}" {{ old('assigned_plumber_id') == $plumber->id ? 'selected' : '' }}>
						{{ $plumber->full_name }} ({{ $plumber->customer_number }}) - {{ $plumber->is_available ? 'Available' : 'Busy' }}
					</option>
				@endforeach
			</select>
			@error('assigned_plumber_id')
				<p class="text-red-500 text-xs mt-1">{{ $message }}</p>
			@enderror
			<p class="text-xs text-gray-500 mt-1">If a plumber is selected, they will be notified about the assignment and a water connection will be created.</p>
		</div>
		<div class="grid grid-cols-2 gap-4">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
				<input type="text" name="first_name" required class="w-full border border-gray-300 px-3 py-1.5 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-500 @enderror" value="{{ old('first_name') }}" onkeypress="return /[a-zA-Z\s]/i.test(event.key)" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
				@error('first_name')
					<p class="text-red-500 text-xs mt-1">{{ $message }}</p>
				@enderror
				<p class="text-xs text-gray-500 mt-1">Letters and spaces only</p>
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
				<input type="text" name="last_name" required class="w-full border border-gray-300 px-3 py-1.5 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-500 @enderror" value="{{ old('last_name') }}" onkeypress="return /[a-zA-Z\s]/i.test(event.key)" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
				@error('last_name')
					<p class="text-xs text-red-500 mt-1">{{ $message }}</p>
				@enderror
				<p class="text-xs text-gray-500 mt-1">Letters and spaces only</p>
			</div>
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
			<input type="email" name="email" required class="w-full border border-gray-300 px-3 py-1.5 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror" value="{{ old('email') }}">
			@error('email')
				<p class="text-xs text-red-500 mt-1">{{ $message }}</p>
			@enderror
		</div>
		<div class="grid grid-cols-2 gap-4">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
				<input type="number" name="age" min="18" max="120" required class="w-full border border-gray-300 px-3 py-1.5 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('age') border-red-500 @enderror" value="{{ old('age') }}">
				@error('age')
					<p class="text-xs text-red-500 mt-1">{{ $message }}</p>
				@enderror
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
				<input type="text" name="phone_number" required maxlength="11" class="w-full border border-gray-300 px-3 py-1.5 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone_number') border-red-500 @enderror" value="{{ old('phone_number') }}" onkeypress="return /[0-9]/i.test(event.key)" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)" placeholder="09XXXXXXXXX">
				@error('phone_number')
					<p class="text-xs text-red-500 mt-1">{{ $message }}</p>
				@enderror
				<p class="text-xs text-gray-500 mt-1">Must be exactly 11 digits</p>
			</div>
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
			<textarea name="address" rows="2.5" required class="w-full border border-gray-300 px-3 py-1.5 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
			@error('address')
				<p class="text-xs text-red-500 mt-1">{{ $message }}</p>
			@enderror
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Temporary Password</label>
			<input type="password" name="password" required minlength="8" class="w-full border border-gray-300 px-3 py-1.5 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" value="{{ old('password') }}">
			@error('password')
				<p class="text-red-500 text-xs mt-1">{{ $message }}</p>
			@enderror
			<p class="text-xs text-gray-500 mt-1">Must be at least 8 characters with at least one uppercase letter and one special character</p>
		</div>
		<div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
			<p class="text-sm text-blue-800">
				<strong>Note:</strong> A unique customer number will be automatically generated in the format YYYY-XXXX (e.g., 2025-0001, 2025-0002, etc.)
			</p>
		</div>
		<div class="text-right">
			<button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">Create</button>
		</div>
	</form>
</div>

<script>
function togglePlumberSelection() {
    const roleSelect = document.getElementById('role');
    const plumberAssignment = document.getElementById('plumber-assignment');
    
    if (roleSelect.value === 'customer') {
        plumberAssignment.style.display = 'block';
    } else {
        plumberAssignment.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePlumberSelection();
});
</script>
@endsection