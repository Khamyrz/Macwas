<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdminRegisteredUserController extends Controller
{
    /**
     * Display the admin registration view.
     */
    public function create(): View
    {
        return view('auth.admin-register');
    }

    /**
     * Handle an incoming admin registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'age' => 18,
            'phone_number' => 'N/A',
            'national_id' => 'ADMIN-'.uniqid(),
            'address' => 'N/A',
            'status' => 'active',
            'is_available' => true,
            'customer_number' => User::generateCustomerNumber(),
            'email_verified_at' => now(), // Admin users are automatically verified
        ]);

        Auth::login($user);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Admin account created successfully! You are now logged in.');
    }
}





