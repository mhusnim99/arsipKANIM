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
        'terisi',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'kapasitas' => 'integer',
        'terisi' => 'integer',
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

    public function masihAdaSlot(): bool
    {
        return $this->terisi < $this->kapasitas;
    }

    /* ================= SYNC STATUS ================= */

    public function syncStatus(): void
    {
        if ($this->terisi >= $this->kapasitas) {
            $this->status = 'penuh';
        } else {
            $this->status = 'tersedia';
        }

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
            'tersedia' => 'success',
            'penuh'    => 'danger',
            default    => 'secondary',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }
}
