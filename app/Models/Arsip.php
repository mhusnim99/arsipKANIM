<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Arsip extends Model
{
    protected $table = 'arsips';

    protected $fillable = [
        'nomor_arsip',
        'pengiriman_berkas_id',
        'kode_permohonan',
        'tanggal_masuk',
        'asal_berkas',
        'keterangan',
        'lemari_id',
        'loker_id',
        'petugas_pengirim_id',
        'diterima_oleh',
        'status',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /* =====================
     | RELATIONS
     ===================== */

    public function pengiriman(): BelongsTo
    {
        return $this->belongsTo(PengirimanBerkas::class, 'pengiriman_berkas_id');
    }
    public function loker()
    {
        return $this->belongsTo(Loker::class, 'loker_id');
    }

    public function lemari()
    {
        return $this->belongsTo(Lemari::class, 'lemari_id');
    }
    public function petugasPengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_pengirim_id');
    }

    public function petugasPenerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    /* =====================
     | SCOPES
     ===================== */

    public function scopeAktif($query)
    {
        return $query->where('status', 'tersimpan');
    }

    /* =====================
     | ACCESSORS
     ===================== */

    public function getLokasiLengkapAttribute(): string
    {
        if ($this->lemari && $this->loker) {
            return "{$this->lemari->kode_lemari}.{$this->loker->kolom}{$this->loker->baris_formatted}";
        }

        return '-';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'tersimpan' => 'success',
            'dipinjam'  => 'warning',
            'hilang'    => 'danger',
            'musnah'    => 'secondary',
            default     => 'secondary',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }
    public function getTanggalMasukFormatAttribute(): string
    {
        return $this->tanggal_masuk
            ? $this->tanggal_masuk->format('d/m/Y')
            : '-';
    }
}
