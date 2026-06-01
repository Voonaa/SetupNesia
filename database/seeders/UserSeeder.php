<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Admin Account
        User::updateOrCreate(
            ['email' => 'admin@setupnesia.com'],
            [
                'name' => 'SetupNesia Admin',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Seed Customer Account
        User::updateOrCreate(
            ['email' => 'customer@setupnesia.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('Customer123!'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );

        // Seed Another Customer Account
        User::updateOrCreate(
            ['email' => 'jane@setupnesia.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('Customer123!'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );
    }
}
