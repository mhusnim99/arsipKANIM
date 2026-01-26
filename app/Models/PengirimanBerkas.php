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
        'loker_id'
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'diterima_pada' => 'datetime',
        'ditolak_pada' => 'datetime',
    ];

    /**
     * Relasi ke user (petugas yang mengirim)
     */
    public function petugasPengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_pengirim_id');
    }

    /**
     * Relasi ke user (petugas yang menerima)
     */
    public function petugasPenerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    /**
     * Relasi ke lemari
     */
    public function lemari(): BelongsTo
    {
        return $this->belongsTo(Lemari::class, 'lemari_id');
    }

    /**
     * Relasi ke loker
     */
    public function loker(): BelongsTo
    {
        return $this->belongsTo(Loker::class, 'loker_id');
    }

    /**
     * Relasi ke arsip (jika sudah diterima)
     */
    public function arsip(): HasOne
    {
        return $this->hasOne(Arsip::class, 'pengiriman_berkas_id');
    }
    /**
     * Scope untuk pengiriman yang menunggu
     */
    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    /**
     * Scope untuk pengiriman yang diterima
     */
    public function scopeDiterima($query)
    {
        return $query->where('status', 'diterima');
    }

    /**
     * Scope untuk pengiriman yang ditolak
     */
    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'menunggu' => 'warning',
            'diterima' => 'success',
            'ditolak' => 'danger'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $texts = [
            'menunggu' => 'Menunggu',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak'
        ];

        return $texts[$this->status] ?? $this->status;
    }

    /**
     * Format tanggal penerimaan
     */
    public function getDiterimaFormatAttribute()
    {
        return $this->diterima_pada ? $this->diterima_pada->format('d-m-Y H:i') : '-';
    }

    /**
     * Format tanggal penolakan
     */
    public function getDitolakFormatAttribute()
    {
        return $this->ditolak_pada ? $this->ditolak_pada->format('d-m-Y H:i') : '-';
    }

    /**
     * Cek apakah bisa diterima
     */
    public function canBeAccepted()
    {
        return $this->status === 'menunggu';
    }

    /**
     * Cek apakah bisa ditolak
     */
    public function canBeRejected()
    {
        return $this->status === 'menunggu';
    }
}
