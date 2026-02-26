<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loker extends Model
{
    protected $table = 'lokers';

    protected $fillable = [
        'kode_loker',
        'lemari_id',
        'kapasitas',
        'kolom',
        'baris',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'kapasitas' => 'integer',
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

    /* ================= HELPER ================= */

    public function jumlahArsip(): int
    {
        return $this->arsips()->count();
    }

    public function masihAdaSlot(): bool
    {
        return $this->jumlahArsip() < $this->kapasitas;
    }

    /* ================= SYNC STATUS ================= */

    public function syncStatus(): void
    {
        if ($this->status === 'nonaktif') {
            return;
        }

        $this->status = $this->jumlahArsip() >= $this->kapasitas
            ? 'penuh'
            : 'aktif';

        $this->save();
    }

    /* ================= DISPLAY ================= */

    public function getDisplayAttribute(): string
    {
        $this->loadMissing('lemari');
        return "{$this->lemari->kode_lemari}.{$this->kolom}{$this->baris}";
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'aktif'     => 'success',
            'penuh'     => 'danger',
            'nonaktif'  => 'secondary',
            default     => 'secondary',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }
}