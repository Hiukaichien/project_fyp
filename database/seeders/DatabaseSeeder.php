<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Import the Hash facade

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // This will find the user with the email 'test@example.com' and update them,
        // or create them if they don't exist. This prevents the unique constraint error.
        User::updateOrCreate(
            [
                'email' => 'test@example.com' // The unique attribute to find the user by
            ],
            [
                'name' => 'Test User',
                'username' => 'test',
                'password' => Hash::make('password'), // Set a default password
                'email_verified_at' => now(), // Optional: verify the user automatically
            ]
        );
    }
}