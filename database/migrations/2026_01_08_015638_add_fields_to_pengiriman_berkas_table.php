<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengiriman_berkas', function (Blueprint $table) {
            // 1. Rename petugas_id jika ada
            if (Schema::hasColumn('pengiriman_berkas', 'petugas_id')) {
                $table->renameColumn('petugas_id', 'petugas_pengirim_id');
            }

            // 2. Tambah kolom baru jika belum ada
            if (!Schema::hasColumn('pengiriman_berkas', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable()->after('status');
            }

            // 3. HANYA tambah arsip_id jika belum ada - HAPUS baris sebelumnya yang duplicate
            if (!Schema::hasColumn('pengiriman_berkas', 'arsip_id')) {
                $table->foreignId('arsip_id')->nullable()->after('alasan_penolakan');
            }

            // 4. Tambah kolom lain yang diperlukan
            if (!Schema::hasColumn('pengiriman_berkas', 'lemari_id')) {
                $table->foreignId('lemari_id')->nullable()->after('arsip_id');
            }

            if (!Schema::hasColumn('pengiriman_berkas', 'loker_id')) {
                $table->foreignId('loker_id')->nullable()->after('lemari_id');
            }

            if (!Schema::hasColumn('pengiriman_berkas', 'nomor_arsip')) {
                $table->string('nomor_arsip')->nullable()->after('loker_id');
            }

            if (!Schema::hasColumn('pengiriman_berkas', 'diterima_pada')) {
                $table->timestamp('diterima_pada')->nullable()->after('nomor_arsip');
            }

            if (!Schema::hasColumn('pengiriman_berkas', 'ditolak_pada')) {
                $table->timestamp('ditolak_pada')->nullable()->after('diterima_pada');
            }

            if (!Schema::hasColumn('pengiriman_berkas', 'diterima_oleh')) {
                $table->foreignId('diterima_oleh')->nullable()->constrained('users')->onDelete('set null')->after('ditolak_pada');
            }

            if (!Schema::hasColumn('pengiriman_berkas', 'ditolak_oleh')) {
                $table->foreignId('ditolak_oleh')->nullable()->constrained('users')->onDelete('set null')->after('diterima_oleh');
            }
        });

        // 5. Setelah semua kolom ditambahkan, baru tambahkan foreign key constraint untuk arsip_id
        Schema::table('pengiriman_berkas', function (Blueprint $table) {
            // Hanya tambah foreign key jika kolom arsip_id ada dan belum ada constraint
            if (Schema::hasColumn('pengiriman_berkas', 'arsip_id')) {
                $table->foreign('arsip_id')->references('id')->on('arsips')->onDelete('set null');
            }

            if (Schema::hasColumn('pengiriman_berkas', 'lemari_id')) {
                $table->foreign('lemari_id')->references('id')->on('lemaris')->onDelete('set null');
            }

            if (Schema::hasColumn('pengiriman_berkas', 'loker_id')) {
                $table->foreign('loker_id')->references('id')->on('lokers')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengiriman_berkas', function (Blueprint $table) {
            // Drop foreign keys terlebih dahulu
            $table->dropForeign(['arsip_id']);
            $table->dropForeign(['lemari_id']);
            $table->dropForeign(['loker_id']);
            $table->dropForeign(['diterima_oleh']);
            $table->dropForeign(['ditolak_oleh']);

            // Rename kembali
            if (Schema::hasColumn('pengiriman_berkas', 'petugas_pengirim_id')) {
                $table->renameColumn('petugas_pengirim_id', 'petugas_id');
            }

            // Drop columns
            $columnsToDrop = ['alasan_penolakan', 'arsip_id', 'lemari_id', 'loker_id',
                            'nomor_arsip', 'diterima_pada', 'ditolak_pada',
                            'diterima_oleh', 'ditolak_oleh'];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('pengiriman_berkas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
