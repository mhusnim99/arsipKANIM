<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pengiriman_berkas', function (Blueprint $table) {
            $table->foreignId('berita_acara_id')
                  ->nullable()
                  ->constrained('berita_acaras')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengiriman_berkas', function (Blueprint $table) {
            $table->dropForeign(['berita_acara_id']);
            $table->dropColumn('berita_acara_id');
        });
    }
};