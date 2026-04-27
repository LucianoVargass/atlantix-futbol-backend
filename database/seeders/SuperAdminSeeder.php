<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@atlantix.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('secret123'),
                'role' => 'super_admin',
                'phone' => null,
            ]
        );
    }
}
