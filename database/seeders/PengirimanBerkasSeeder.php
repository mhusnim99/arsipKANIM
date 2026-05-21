<?php

namespace Database\Seeders;

use App\Models\PengirimanBerkas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PengirimanBerkasSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        PengirimanBerkas::query()->delete();

        DB::statement('ALTER TABLE pengiriman_berkas AUTO_INCREMENT = 1');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('✓ Tabel pengiriman_berkas berhasil dikosongkan');

        $petugas = User::where('role', 'user')->first();

        if (!$petugas) {
            $petugas = User::create([
                'name' => 'Petugas Layanan',
                'email' => 'petugas@kanim.com',
                'password' => bcrypt('petugas123'),
                'role' => 'user',
            ]);
            $this->command->info('✓ User petugas layanan dibuat: ' . $petugas->email);
        }

        $successCount = 0;
        $stats = PengirimanBerkas::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $this->command->info("\n📈 Statistik Status:");
        foreach ($stats as $status => $total) {
            $this->command->info("   {$status}: {$total} data");
        }
    }
}
