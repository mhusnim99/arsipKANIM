<?php

namespace App\Models;

use App\Models\Arsip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loker extends Model
{
    protected $table = 'lokers';

    protected $fillable = [
        'kode_loker',
        'lemari_id',
        'kolom',
        'baris',
        'kapasitas',
        'status',
        'keterangan',
    ];

    /* ================= RELATION ================= */

    public function lemari(): BelongsTo
    {
        return $this->belongsTo(Lemari::class);
    }

    public function arsips()
    {
        return $this->hasMany(Arsip::class);
    }

    /* ================= SCOPE ================= */

    public function scopeTersedia($query)
    {
        return $query->where('status', 'aktif');
    }

    /* ================= HELPER ================= */

    public function jumlahArsip(): int
    {
        return $this->arsips()->count();
    }

    public function masihAdaSlot(): bool
    {
        return $this->jumlahArsip() < $this->kapasitas;
    }

    /**
     * Sinkronisasi status loker (WAJIB DIPANGGIL)
     */
    public function syncStatus(): void
    {
        if ($this->status === 'nonaktif') return;

        $this->update([
            'status' => $this->jumlahArsip() >= $this->kapasitas
                ? 'penuh'
                : 'aktif'
        ]);
    }


    /* ================= DISPLAY ================= */

    public function getBarisFormattedAttribute(): string
    {
        return str_pad($this->baris, 2, '0', STR_PAD_LEFT);
    }

    public function getDisplayAttribute(): string
    {
        $this->loadMissing('lemari');

        return "{$this->lemari->kode_lemari}.{$this->kolom}{$this->baris}";
    }

    /* ================= BADGE ================= */

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'aktif'     => 'success',
            'penuh'     => 'warning',
            'nonaktif'  => 'secondary',
            default     => 'secondary',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'aktif'     => 'Aktif',
            'penuh'     => 'Penuh',
            'nonaktif'  => 'Nonaktif',
            default     => ucfirst($this->status),
        };
    }

    /* ================= NOMOR ARSIP ================= */

    public function generateNomorArsip(): string
    {
        $this->loadMissing('lemari');

        $urutan = $this->arsips()->count() + 1;
        $urutanFormatted = str_pad($urutan, 4, '0', STR_PAD_LEFT);

        return "{$this->lemari->kode_lemari}.{$this->kolom}{$this->baris}.{$urutanFormatted}";
    }
}
