// database/migrations/2024_01_01_000002_create_lemaris_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLemarisTable extends Migration
{
    public function up()
    {
        Schema::create('lemaris', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor_lemari')->unique();
            $table->string('nama_lemari', 100);
            $table->integer('jumlah_kolom')->default(3);
            $table->integer('jumlah_baris_per_kolom')->default(10);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'penuh'])->default('aktif');
            $table->integer('kapasitas_total')->default(30);
            $table->integer('terisi')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lemaris');
    }
}
