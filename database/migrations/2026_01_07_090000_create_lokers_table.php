// database/migrations/2024_01_01_000003_create_lokers_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLokersTable extends Migration
{
    public function up()
    {
        Schema::create('lokers', function (Blueprint $table) {
            $table->id();

            $table->string('kode_loker', 30)->unique();

            $table->foreignId('lemari_id')
                ->constrained('lemaris')
                ->onDelete('cascade');

            $table->string('kolom', 1); // A, B, C
            $table->integer('baris');   // 1–10

            // ✅ STATUS KHUSUS LOKER
            $table->enum('status', ['nonaktif', 'penuh', 'aktif'])->default('aktif');

            // ✅ KAPASITAS DINAMIS PER LOKER
            $table->integer('kapasitas')->default(1);

            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->unique(['lemari_id', 'kolom', 'baris']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lokers');
    }
}
