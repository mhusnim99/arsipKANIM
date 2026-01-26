<?php

namespace Database\Seeders;

use App\Models\Arsip;
use App\Models\Lemari;
use App\Models\Loker;
use App\Models\User;
use App\Models\PengirimanBerkas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ArsipSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Arsip::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $lemari = Lemari::first();
        $petugasArsip = User::where('role', 'admin')->first();
        $pengirimanDiterima = PengirimanBerkas::where('status', 'diterima')->get();

        if (!$lemari || !$petugasArsip || $pengirimanDiterima->isEmpty()) {
            $this->command->error('✗ Data tidak lengkap');
            return;
        }

        foreach ($pengirimanDiterima as $index => $pengiriman) {

            // ambil loker kosong
            $loker = Loker::where('lemari_id', $lemari->id)
                          ->where('status', 'kosong')
                          ->first();

            if (!$loker) {
                $this->command->warn('⚠ Loker habis, arsip dihentikan');
                break;
            }

            // 1️⃣ BUAT ARSIP DULU
            $arsip = Arsip::create([
                'kode_permohonan'  => $pengiriman->kode_permohonan,
                'pengiriman_id'    => $pengiriman->id,
                'lemari_id'        => $lemari->id,
                'loker_id'         => $loker->id,
                'nomor_arsip'      => 'ARS-' . now()->format('Ymd') . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'tanggal_terima'   => now(),
                'petugas_arsip_id' => $petugasArsip->id,
                'status'           => 'tersimpan',
                'keterangan'       => 'Arsip hasil seeding',
            ]);

            // 2️⃣ BARU update pengiriman
            $pengiriman->update([
                'arsip_id' => $arsip->id
            ]);

            // 3️⃣ Update loker
            $loker->update(['status' => 'terisi']);
        }

        $this->command->info('✅ ArsipSeeder berhasil tanpa FK error');
    }
}
