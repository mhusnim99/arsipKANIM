<?php
// database/migrations/2026_01_07_xxxxxx_create_pengiriman_berkas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman_berkas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_permohonan')->unique();
            $table->date('tanggal_kirim');
            $table->enum('asal_berkas', [
                'KANIM',
                'ULP LTSA MPP Sidoarjo',
                'Immgration Lounge CIWO',
                'ULP Bendul Merisi',
                'ULP Wiyung',
                'ULP BG Junction',
                'ULP Mojokerto'
            ]);
            $table->enum('status', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->foreignId('petugas_pengirim_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
            $table->index('kode_permohonan');
            $table->index('status');
            $table->index(['tanggal_kirim', 'asal_berkas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman_berkas');
    }
};
