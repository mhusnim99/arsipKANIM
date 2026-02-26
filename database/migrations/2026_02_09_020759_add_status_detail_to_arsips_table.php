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
    Schema::table('arsips', function (Blueprint $table) {
        $table->string('dipinjam_oleh', 100)->nullable()->after('status');
        $table->text('keperluan')->nullable()->after('dipinjam_oleh');
        $table->dateTime('tanggal_pinjam')->nullable()->after('keperluan');

        $table->string('dimusnahkan_oleh', 100)->nullable()->after('tanggal_pinjam');
        $table->dateTime('tanggal_musnah')->nullable()->after('dimusnahkan_oleh');
    });
}

public function down()
{
    Schema::table('arsips', function (Blueprint $table) {
        $table->dropColumn([
            'dipinjam_oleh',
            'keperluan',
            'tanggal_pinjam',
            'dimusnahkan_oleh',
            'tanggal_musnah',
        ]);
    });
}

};
