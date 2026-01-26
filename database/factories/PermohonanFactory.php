<?php

namespace Database\Factories;

use App\Models\Permohonan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermohonanFactory extends Factory
{
    protected $model = Permohonan::class;

    public function definition(): array
    {
        static $counter = 1;

        $statuses = ['pending', 'diproses', 'selesai', 'ditolak'];
        $alurLokasi = ['L1-A-001', 'L1-A-002', 'L1-B-001', 'L2-A-001', 'L2-B-002'];

        return [
            'nomor_permohonan' => 'REQ' . str_pad($counter++, 5, '0', STR_PAD_LEFT),
            'nomor_paspor' => 'A' . str_pad($this->faker->numberBetween(100000, 999999), 7, '0', STR_PAD_LEFT),
            'nama_pemohon' => $this->faker->name(),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-60 years', '-18 years'),
            'alur_lokasi' => $this->faker->randomElement($alurLokasi),
            'status' => $this->faker->randomElement($statuses),
            'keterangan' => function (array $attributes) {
                return $this->generateKeterangan($attributes['status']);
            },
        ];
    }

    private function generateKeterangan($status): string
    {
        $keterangan = [
            'pending' => 'Permohonan sedang dalam antrian verifikasi',
            'diproses' => 'Permohonan sedang diproses oleh petugas',
            'selesai' => 'Permohonan telah selesai dan arsip tersedia',
            'ditolak' => 'Permohonan tidak memenuhi persyaratan',
        ];

        return $keterangan[$status] ?? 'Tidak ada keterangan';
    }
}
