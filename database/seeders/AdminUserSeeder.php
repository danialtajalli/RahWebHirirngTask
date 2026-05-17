<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Level 1',
            'email' => 'admin1@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN_1
        ]);

        User::create([
            'name' => 'Admin Level 2',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN_2
        ]);
    }
}
