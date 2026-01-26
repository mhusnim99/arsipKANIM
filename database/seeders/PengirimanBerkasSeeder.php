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
        // Nonaktifkan foreign key check sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Kosongkan tabel dengan cara yang aman
        PengirimanBerkas::query()->delete();

        // Reset auto increment
        DB::statement('ALTER TABLE pengiriman_berkas AUTO_INCREMENT = 1');

        // Aktifkan kembali foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('✓ Tabel pengiriman_berkas berhasil dikosongkan');

        // Pastikan ada user dengan role 'user' (petugas layanan)
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

        // Data contoh pengiriman berkas
        $pengirimanData = [
            [
                'kode_permohonan' => 'PMH-2024-001',
                'tanggal_kirim' => Carbon::now()->subDays(5),
                'asal_berkas' => 'KANIM Pusat',
                'status' => 'diterima',
                'catatan' => 'Berkas lengkap, segel rapi, semua dokumen valid',
                'petugas_pengirim_id' => $petugas->id,
                'alasan_penolakan' => null,
                'arsip_id' => null,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'kode_permohonan' => 'PMH-2024-002',
                'tanggal_kirim' => Carbon::now()->subDays(4),
                'asal_berkas' => 'ULP LTSA MPP Sidoarjo',
                'status' => 'menunggu',
                'catatan' => 'Menunggu verifikasi dokumen pendukung',
                'petugas_pengirim_id' => $petugas->id,
                'alasan_penolakan' => null,
                'arsip_id' => null,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'kode_permohonan' => 'PMH-2024-003',
                'tanggal_kirim' => Carbon::now()->subDays(3),
                'asal_berkas' => 'Immgration Lounge CIWO',
                'status' => 'ditolak',
                'catatan' => 'Formulir tidak lengkap, foto tidak sesuai',
                'petugas_pengirim_id' => $petugas->id,
                'alasan_penolakan' => 'Dokumen tidak lengkap dan foto tidak memenuhi syarat',
                'arsip_id' => null,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'kode_permohonan' => 'PMH-2024-004',
                'tanggal_kirim' => Carbon::now()->subDays(2),
                'asal_berkas' => 'ULP Bendul Merisi',
                'status' => 'menunggu',
                'catatan' => null,
                'petugas_pengirim_id' => $petugas->id,
                'alasan_penolakan' => null,
                'arsip_id' => null,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'kode_permohonan' => 'PMH-2024-005',
                'tanggal_kirim' => Carbon::now()->subDays(1),
                'asal_berkas' => 'ULP Wiyung',
                'status' => 'diterima',
                'catatan' => 'Sudah diverifikasi, siap diproses arsip',
                'petugas_pengirim_id' => $petugas->id,
                'alasan_penolakan' => null,
                'arsip_id' => 1,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'kode_permohonan' => 'PMH-2024-006',
                'tanggal_kirim' => Carbon::now()->subDays(6),
                'asal_berkas' => 'ULP BG Junction',
                'status' => 'menunggu',
                'catatan' => 'Berkas dari BG Junction, perlu pengecekan ulang',
                'petugas_pengirim_id' => $petugas->id,
                'alasan_penolakan' => null,
                'arsip_id' => null,
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(6),
            ],
            [
                'kode_permohonan' => 'PMH-2024-007',
                'tanggal_kirim' => Carbon::now()->subDays(7),
                'asal_berkas' => 'ULP Mojokerto',
                'status' => 'menunggu',
                'catatan' => 'Perlu validasi tambahan dari supervisor',
                'petugas_pengirim_id' => $petugas->id,
                'alasan_penolakan' => null,
                'arsip_id' => null,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'kode_permohonan' => 'PMH-2024-008',
                'tanggal_kirim' => Carbon::now()->subDays(2),
                'asal_berkas' => 'KANIM Cabang',
                'status' => 'menunggu',
                'catatan' => 'Berkas urgent, segera proses',
                'petugas_pengirim_id' => $petugas->id,
                'alasan_penolakan' => null,
                'arsip_id' => null,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'kode_permohonan' => 'PMH-2024-009',
                'tanggal_kirim' => Carbon::now()->subDays(1),
                'asal_berkas' => 'ULP Tunjungan Plaza',
                'status' => 'menunggu',
                'catatan' => 'Dokumen lengkap, tinggal verifikasi akhir',
                'petugas_pengirim_id' => $petugas->id,
                'alasan_penolakan' => null,
                'arsip_id' => null,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'kode_permohonan' => 'PMH-2024-010',
                'tanggal_kirim' => Carbon::now(),
                'asal_berkas' => 'ULP Surabaya Plaza',
                'status' => 'menunggu',
                'catatan' => 'Baru dikirim pagi ini',
                'petugas_pengirim_id' => $petugas->id,
                'alasan_penolakan' => null,
                'arsip_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        $successCount = 0;
        foreach ($pengirimanData as $data) {
            try {
                PengirimanBerkas::create($data);
                $this->command->info("✓ Berhasil: {$data['kode_permohonan']} - {$data['asal_berkas']} ({$data['status']})");
                $successCount++;
            } catch (\Exception $e) {
                $this->command->error("✗ Gagal {$data['kode_permohonan']}: " . $e->getMessage());
            }
        }

        $this->command->info("\n✅ Seeder Pengiriman Berkas selesai!");
        $this->command->info("📊 Total data: " . count($pengirimanData));
        $this->command->info("✅ Berhasil: $successCount");
        $this->command->info("❌ Gagal: " . (count($pengirimanData) - $successCount));

        // Tampilkan statistik
        $stats = PengirimanBerkas::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $this->command->info("\n📈 Statistik Status:");
        foreach ($stats as $status => $total) {
            $this->command->info("   {$status}: {$total} data");
        }
    }
}
