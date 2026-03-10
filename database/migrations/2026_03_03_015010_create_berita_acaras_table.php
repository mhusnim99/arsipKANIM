<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('berita_acaras', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_berita_acara')->unique();
            $table->foreignId('petugas_pengirim_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->date('tanggal_dibuat');
            $table->integer('jumlah_arsip');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita_acaras');
    }
};