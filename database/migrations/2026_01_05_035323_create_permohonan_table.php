<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permohonan')->unique();
            $table->string('nomor_paspor');
            $table->string('nama_pemohon');
            $table->date('tanggal_lahir');
            $table->string('alur_lokasi')->nullable();
            $table->enum('status', ['pending', 'diproses', 'selesai', 'ditolak'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Index untuk pencarian cepat
            $table->index('nomor_permohonan');
            $table->index('nomor_paspor');
            $table->index('nama_pemohon');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan');
    }
};
