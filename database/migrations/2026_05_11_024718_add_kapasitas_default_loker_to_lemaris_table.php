<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lemaris', function (Blueprint $table) {

            $table->integer('kapasitas_default_loker')
                ->default(350)
                ->after('jumlah_baris_per_kolom');

        });
    }

    public function down(): void
    {
        Schema::table('lemaris', function (Blueprint $table) {

            $table->dropColumn('kapasitas_default_loker');

        });
    }
};