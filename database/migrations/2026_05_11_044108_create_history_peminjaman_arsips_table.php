<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_peminjaman_arsips', function (Blueprint $table) {

            $table->id();
            $table->foreignId('arsip_id')
                ->constrained('arsips')
                ->cascadeOnDelete();

            $table->string('peminjam');

            $table->text('keperluan')
                ->nullable();

            $table->timestamp('tanggal_pinjam');

            $table->timestamp('tanggal_kembali')
                ->nullable();

            $table->foreignId('diproses_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_peminjaman_arsips');
    }
};