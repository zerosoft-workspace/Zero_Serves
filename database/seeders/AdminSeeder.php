<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Eğer admin yoksa oluştur
        $user = User::firstOrCreate(
            ['email' => 'admin@demo.com'], // Kontrol edilecek alan
            [
                'name' => 'Admin',
                'password' => bcrypt('secret'), // Şifre: secret
            ]
        );

        // Eğer Spatie roles kullanıyorsan:
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('admin');
        }
    }
}
