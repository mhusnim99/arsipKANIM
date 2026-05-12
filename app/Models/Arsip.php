<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\HistoryPeminjamanArsip;

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
        'dipinjam_oleh',
        'keperluan',
        'tanggal_pinjam',
        'dimusnahkan_oleh',
        'tanggal_musnah',
        'nama_lengkap',
        'nomor_paspor',
        'tanggal_permohonan',
        'status_proses',
        'slot',
    ];

    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'tanggal_permohonan' => 'date',
        'tanggal_pinjam' => 'datetime',
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
            'musnah'    => 'danger',
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
    public function getSlotRangeAttribute()
    {
        $start = ($this->slot - 1) * 10 + 1;
        $end = $this->slot * 10;

        return "{$start}-{$end}";
    }
    public function scopeSiapMusnah($query)
    {
        return $query->where('created_at', '<=', now()->subYear());
    }
    public function histories(): HasMany
    {
        return $this->hasMany(HistoryPeminjamanArsip::class);
    }
}
