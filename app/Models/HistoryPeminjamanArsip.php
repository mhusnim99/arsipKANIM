<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryPeminjamanArsip extends Model
{
    protected $fillable = [
        'arsip_id',
        'peminjam',
        'keperluan',
        'tanggal_pinjam',
        'tanggal_kembali',
        'diproses_oleh',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali' => 'datetime',
    ];

    public function arsip()
    {
        return $this->belongsTo(Arsip::class);
    }
}