<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model
{
    protected $fillable = [
        'nomor_berita_acara',
        'petugas_pengirim_id',
        'tanggal_dibuat',
        'jumlah_arsip',
        'file_path',
    ];

    protected $casts = [
        'tanggal_dibuat' => 'datetime',
    ];

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'petugas_pengirim_id');
    }

    public function pengirimanBerkas()
    {
        return $this->hasMany(PengirimanBerkas::class);
    }
}