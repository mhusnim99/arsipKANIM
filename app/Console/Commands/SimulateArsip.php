<?php

namespace App\Console\Commands;

use App\Models\PengirimanBerkas;
use App\Models\Arsip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SimulateArsip extends Command
{
    protected $signature = 'simulate:arsip {jumlah=1000}';
    protected $description = 'Simulasi pengisian arsip';

    public function handle()
    {
        $jumlah = $this->argument('jumlah');

        DB::transaction(function () use ($jumlah) {

            for ($i = 0; $i < $jumlah; $i++) {

                $loker = \App\Services\LokerAllocator::getAvailableLoker();

                if (!$loker) {
                    $this->error('SEMUA LOKER PENUH');
                    break;
                }

                $count = Arsip::where('loker_id', $loker->id)->count();
                $nomorUrut = $count + 1;

                $nomor = sprintf(
                    "%s.%s.%04d",
                    $loker->lemari->kode_lemari,
                    $loker->kode_loker,
                    $nomorUrut
                );

                Arsip::create([
                    'pengiriman_berkas_id' => 1,
                    'kode_permohonan' => rand(100000, 999999),
                    'nomor_arsip' => $nomor,
                    'tanggal_masuk' => now(),
                    'asal_berkas' => 'SIMULASI',
                    'lemari_id' => $loker->lemari_id,
                    'loker_id' => $loker->id,
                    'slot' => ceil($nomorUrut / 10),
                    'status' => 'tersimpan',
                    'diterima_oleh' => 1,

                    // 🔥 TAMBAHKAN INI
                    'nama_lengkap' => 'TEST USER',
                    'nomor_paspor' => 'SIM123456',
                    'tanggal_permohonan' => now(),
                    'status_proses' => 'SELESAI',
                ]);
                $loker->syncStatus();
                $loker->lemari->syncStatus();
            }
        });

        $this->info("Selesai simulasi {$jumlah} arsip");
    }
}
