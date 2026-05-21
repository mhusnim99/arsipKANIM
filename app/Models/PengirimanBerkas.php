<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengirimanBerkas extends Model
{
    protected $table = 'pengiriman_berkas';

    protected $fillable = [
        'kode_permohonan',
        'tanggal_kirim',
        'asal_berkas',
        'catatan',
        'petugas_pengirim_id',
        'status',
        'alasan_penolakan',
        'arsip_id',
        'diterima_oleh',
        'diterima_pada',
        'ditolak_pada',
        'nomor_arsip',
        'lemari_id',
        'loker_id',
        'simkim_snapshot',
        'berita_acara_id',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'diterima_pada' => 'datetime',
        'ditolak_pada' => 'datetime',
        'simkim_snapshot' => 'array',
    ];

    public function petugasPengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_pengirim_id');
    }

    public function petugasPenerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    public function lemari(): BelongsTo
    {
        return $this->belongsTo(Lemari::class, 'lemari_id');
    }

    public function loker(): BelongsTo
    {
        return $this->belongsTo(Loker::class, 'loker_id');
    }

    public function arsip(): HasOne
    {
        return $this->hasOne(Arsip::class, 'pengiriman_berkas_id');
    }

    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeDiterima($query)
    {
        return $query->where('status', 'diterima');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'menunggu' => 'warning',
            'diterima' => 'success',
            'ditolak' => 'danger'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'menunggu' => 'Menunggu',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak'
        ];

        return $texts[$this->status] ?? $this->status;
    }

    public function getDiterimaFormatAttribute()
    {
        return $this->diterima_pada ? $this->diterima_pada->format('d-m-Y H:i') : '-';
    }

    public function getDitolakFormatAttribute()
    {
        return $this->ditolak_pada ? $this->ditolak_pada->format('d-m-Y H:i') : '-';
    }

    public function beritaAcara()
    {
        return $this->belongsTo(BeritaAcara::class);
    }
}
