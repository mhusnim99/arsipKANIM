<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Perpanjang kolom asal_berkas menjadi 255 karakter
        Schema::table('pengiriman_berkas', function (Blueprint $table) {
            $table->string('asal_berkas', 255)->change();
        });
    }

    public function down()
    {
        // Kembalikan ke panjang semula jika rollback
        Schema::table('pengiriman_berkas', function (Blueprint $table) {
            $table->string('asal_berkas', 50)->change();
        });
    }
};
