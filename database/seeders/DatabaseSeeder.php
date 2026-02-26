<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\LemariSeeder;
use Database\Seeders\LokerSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        // ========== SEEDER UNTUK USER ==========
        $this->call([
            KantorUserSeeder::class,
            // LemariLokerSeeder::class,
            PengirimanBerkasSeeder::class,
            ArsipSeeder::class,
        ]);

        // Atau langsung buat user di sini (alternatif):
        // $this->createUsers();
        // Aktifkan kembali foreign key check
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('🎉 Semua seeder berhasil dijalankan!');
    }
}
