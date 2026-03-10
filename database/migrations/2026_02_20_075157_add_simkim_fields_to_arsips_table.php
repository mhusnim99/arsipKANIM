<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSimkimFieldsToArsipsTable extends Migration
{
    public function up()
    {
        Schema::table('arsips', function (Blueprint $table) {

            // ===============================
            // FIELD SIMKIM TERSTRUKTUR
            // ===============================

            $table->string('nama_lengkap')->after('kode_permohonan');
            $table->string('nomor_paspor')->nullable()->after('nama_lengkap');
            $table->date('tanggal_permohonan')->nullable()->after('nomor_paspor');
            $table->string('status_proses')->nullable()->after('tanggal_permohonan');

            // ===============================
            // INDEX UNTUK PENCARIAN CEPAT
            // ===============================

            $table->index('nama_lengkap');
            $table->index('nomor_paspor');
            $table->index('status_proses');
        });
    }

    public function down()
    {
        Schema::table('arsips', function (Blueprint $table) {

            $table->dropIndex(['nama_lengkap']);
            $table->dropIndex(['nomor_paspor']);
            $table->dropIndex(['status_proses']);

            $table->dropColumn([
                'nama_lengkap',
                'nomor_paspor',
                'tanggal_permohonan',
                'status_proses',
            ]);
        });
    }
}