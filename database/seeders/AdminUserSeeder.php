<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'password' => Hash::make('password123'), // Change this to a secure password
            'is_admin' => 1, // Set as admin
        ]);
    }
}
