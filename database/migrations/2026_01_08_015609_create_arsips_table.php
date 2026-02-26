// database/migrations/xxxx_xx_xx_xxxxxx_create_arsips_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArsipsTable extends Migration
{
    public function up()
    {
        Schema::create('arsips', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_arsip')->unique();
            $table->foreignId('pengiriman_berkas_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('kode_permohonan');
            $table->date('tanggal_masuk');
            $table->string('asal_berkas');
            $table->text('keterangan')->nullable();

            // Foreign keys
            $table->foreignId('lemari_id')->constrained('lemaris')->onDelete('cascade');
            $table->foreignId('loker_id')->constrained('lokers')->onDelete('cascade');
            $table->foreignId('petugas_pengirim_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('diterima_oleh')->nullable()->constrained('users')->onDelete('set null');

            $table->enum('status', ['tersimpan', 'dipinjam', 'musnah'])->default('tersimpan');
            $table->timestamps();

            // Indexes
            $table->index('kode_permohonan');
            $table->index('nomor_arsip');
        });
    }

    public function down()
    {
        Schema::dropIfExists('arsips');
    }
}
