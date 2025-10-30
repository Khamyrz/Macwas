<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'Hunt',
            'password' => Hash::make('Entity'),
            'role' => 'admin',
            'age' => 30,
            'phone_number' => '1234567890',
            'national_id' => 'ADMIN001',
            'address' => 'Admin Address',
            'status' => 'active',
            'is_available' => true,
            'email_verified_at' => now(), // Admin users are automatically verified
        ]);
    }
}




