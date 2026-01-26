<?php

namespace Database\Seeders;

use App\Models\Lemari;
use App\Models\Loker;
use Illuminate\Database\Seeder;

class LemariLokerSeeder extends Seeder
{
    public function run()
    {
        // Data lemari default
        $lemaris = [
            [
                'kode_lemari' => 'L1',
                'nama_lemari' => 'Lemari Paspor Reguler',
                'jumlah_kolom' => 3,
                'jumlah_baris_per_kolom' => 10,
                'keterangan' => 'Untuk penyimpanan paspor reguler',
                'status' => 'aktif',
                'kapasitas_total' => 30,
                'terisi' => 5
            ],
            [
                'kode_lemari' => 'L2',
                'nama_lemari' => 'Lemari Paspor Diplomatik',
                'jumlah_kolom' => 2,
                'jumlah_baris_per_kolom' => 8,
                'keterangan' => 'Untuk penyimpanan paspor diplomatik',
                'status' => 'aktif',
                'kapasitas_total' => 16,
                'terisi' => 3
            ],
            [
                'kode_lemari' => 'L3',
                'nama_lemari' => 'Lemari Paspor Hilang',
                'jumlah_kolom' => 3,
                'jumlah_baris_per_kolom' => 6,
                'keterangan' => 'Untuk penyimpanan berkas paspor hilang',
                'status' => 'aktif',
                'kapasitas_total' => 18,
                'terisi' => 2
            ],
            [
                'kode_lemari' => 'L4',
                'nama_lemari' => 'Lemari Arsip Lama',
                'jumlah_kolom' => 4,
                'jumlah_baris_per_kolom' => 12,
                'keterangan' => 'Untuk penyimpanan arsip lebih dari 5 tahun',
                'status' => 'aktif',
                'kapasitas_total' => 48,
                'terisi' => 15
            ],
            [
                'kode_lemari' => 'L5',
                'nama_lemari' => 'Lemari Cadangan',
                'jumlah_kolom' => 3,
                'jumlah_baris_per_kolom' => 10,
                'keterangan' => 'Untuk penyimpanan cadangan',
                'status' => 'nonaktif',
                'kapasitas_total' => 30,
                'terisi' => 0
            ],
        ];

        foreach ($lemaris as $dataLemari) {
            $lemari = Lemari::create($dataLemari);

            // Generate loker untuk setiap lemari
            $lemari->generateLokers();

            // Update beberapa loker menjadi terisi sesuai data
            if ($lemari->terisi > 0) {
                $lokers = $lemari->lokers()->take($lemari->terisi)->get();
                foreach ($lokers as $loker) {
                    $loker->update(['status' => 'terisi']);
                }
            }
        }

        $this->command->info('Seeder Lemari & Loker berhasil dijalankan!');
        $this->command->info('Total lemari: ' . Lemari::count());
        $this->command->info('Total loker: ' . Loker::count());
    }
}
