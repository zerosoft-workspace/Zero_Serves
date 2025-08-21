<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@info.com'],
            [
                'name' => 'Sistem Yöneticisi',
                'password' => Hash::make('123'),
                'role' => 'admin',
            ]
        );
    }
}
