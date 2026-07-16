<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sunsetbagelexchange.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Password123!'),
                'role' => User::ROLE_SUPER_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@sunsetbagelexchange.com'],
            [
                'name' => 'Menu Manager',
                'password' => Hash::make('Password123!'),
                'role' => User::ROLE_MANAGER,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@sunsetbagelexchange.com'],
            [
                'name' => 'Front Counter Staff',
                'password' => Hash::make('Password123!'),
                'role' => User::ROLE_STAFF,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
