<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simkim_syncs', function (Blueprint $table) {
            $table->id();

            // Kode unik dari SIMKIM
            $table->string('kode_permohonan')->unique();

            // Snapshot full JSON dari API
            $table->json('data_snapshot');

            // Status terakhir dari SIMKIM
            $table->string('status_proses')->nullable()->index();

            // Apakah sudah dikirim ke pengiriman_berkas
            $table->boolean('sudah_dikirim')->default(false)->index();

            // Waktu terakhir sync
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simkim_syncs');
    }
};