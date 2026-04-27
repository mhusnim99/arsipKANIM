<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KantorUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'   => 'Admin Gudang',
                'last_name' => 'TKIM',
                'email'  => 'admin@arsip.com',
                'password' => Hash::make('admin123'),
                'role'   => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'kantor' => 'TKIM',
            ],
            [
                'name'   => 'Petugas Kantor Imigrasi Kelas I Khusus TPI Surabaya',
                'last_name' => 'Kanim',
                'email'  => 'kanim@arsip.com',
                'password' => Hash::make('kanim123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'kantor' => 'Kantor Imigrasi Kelas I Khusus TPI Surabaya',
            ],
            [
                'name'   => 'Petugas ULP LTSA MPP Sidoarjo',
                'last_name' => 'ULP LTSA MPP Sidoarjo',
                'email'  => 'mpp@arsip.com',
                'password' => Hash::make('mpp123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'kantor' => 'ULP LTSA MPP Sidoarjo',
            ],
            [
                'name'   => 'Petugas Immigration Lounge CIWO',
                'last_name' => 'Immigration Lounge CIWO',
                'email'  => 'ciwo@arsip.com',
                'password' => Hash::make('ciwo123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'kantor' => 'Immgration Lounge CIWO',
            ],
            [
                'name'   => 'Petugas ULP Bendul Merisi',
                'last_name' => 'ULP Bendul Merisi',
                'email'  => 'bendul@arsip.com',
                'password' => Hash::make('bendul123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'kantor' => 'ULP Bendul Merisi',
            ],
            [
                'name'   => 'Petugas ULP Wiyung',
                'last_name' => 'ULP Wiyung',
                'email'  => 'wiyung@arsip.com',
                'password' => Hash::make('wiyung123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'kantor' => 'ULP Wiyung',
            ],
            [
                'name'   => 'Petugas ULP BG Junction',
                'last_name' => 'ULP BG Junction',
                'email'  => 'bgj@arsip.com',
                'password' => Hash::make('bgj123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'kantor' => 'ULP BG Junction',
            ],
            [
                'name'   => 'Petugas ULP Mojokerto',
                'last_name' => 'ULP Mojokerto',
                'email'  => 'mjk@arsip.com',
                'password' => Hash::make('mjk123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'kantor' => 'ULP Mojokerto',
            ],
            [
                'name'   => 'Petugas Arsip',
                'last_name' => 'Petugas Arsip',
                'email'  => 'petugas@arsip.com',
                'password' => Hash::make('petugas'),
                'role' => 'petugas_arsip',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'kantor' => 'Kantor Imigrasi Kelas I TPI Surabaya',
            ],
            [
                'name'   => 'Admin Layanan',
                'last_name' => 'Admin Layanan',
                'email'  => 'layanan@arsip.com',
                'password' => Hash::make('layanan123'),
                'role' => 'user',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'kantor' => 'TIKIM',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
