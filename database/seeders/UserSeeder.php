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
        // ========== BUAT USER ADMIN ==========
        User::create([
            'name' => 'Administrator',
            'last_name' => 'System', // Tambahkan ini
            'email' => 'admin@kanim.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ========== BUAT USER BIASA ==========
        User::create([
            'name' => 'User',
            'last_name' => 'Biasa', // Tambahkan ini
            'email' => 'user@kanim.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}