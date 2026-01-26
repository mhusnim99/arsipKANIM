<?php

namespace Database\Seeders;

use App\Models\Permohonan;
use Illuminate\Database\Seeder;

class PermohonanSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama jika ada
        Permohonan::truncate();

        // Buat 50 data dummy menggunakan factory
        Permohonan::factory()->count(50)->create();

        echo "✅ Successfully seeded 50 permohonan records using factory!\n";
    }
}
