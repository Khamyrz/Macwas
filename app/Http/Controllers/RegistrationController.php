<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:plumber,accountant,customer',
            'age' => 'required|integer|min:18|max:120',
            'phone_number' => 'required|string|max:20',
            // Accept National ID as an uploaded image file
            'national_id' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'address' => 'required|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = null;
        $nationalIdPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('user-photos', 'public');
        }
        if ($request->hasFile('national_id')) {
            $nationalIdPath = $request->file('national_id')->store('national-ids', 'public');
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'age' => $request->age,
            'phone_number' => $request->phone_number,
            // Store the uploaded National ID image path
            'national_id' => $nationalIdPath,
            'address' => $request->address,
            'photo' => $photoPath,
            'status' => 'pending', // Admin needs to approve
            'is_available' => $request->role === 'plumber', // Plumbers start as available
            'customer_number' => User::generateCustomerNumber(),
        ]);

        return redirect()->route('login')
            ->with('success', 'Successfully created, now wait for the Admin to approve your account.');
    }
}




