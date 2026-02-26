<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pengiriman_berkas', function (Blueprint $table) {
            $table->json('simkim_snapshot')->nullable()->after('asal_berkas');
        });
    }

    public function down()
    {
        Schema::table('pengiriman_berkas', function (Blueprint $table) {
            $table->dropColumn('simkim_snapshot');
        });
    }
};
