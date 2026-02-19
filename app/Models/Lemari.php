<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Lemari extends Model
{
    protected $table = 'lemaris';

    protected $fillable = [
        'kode_lemari',
        'nama_lemari',
        'jumlah_kolom',
        'jumlah_baris_per_kolom',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_kolom' => 'integer',
        'jumlah_baris_per_kolom' => 'integer',
    ];

    /* ================= RELATION ================= */

    public function lokers(): HasMany
    {
        return $this->hasMany(Loker::class);
    }

    public function arsips(): HasManyThrough
    {
        return $this->hasManyThrough(Arsip::class, Loker::class);
    }

    /* ================= HELPER ================= */

    public function kapasitasTotalArsip(): int
    {
        return $this->lokers()->sum('kapasitas');
    }

    public function jumlahArsip(): int
    {
        return $this->arsips()->count();
    }

    public function jumlahLokerTersedia(): int
    {
        return $this->lokers()->where('status', 'tersedia')->count();
    }

    /* ================= GENERATE LOKER ================= */

    public function generateLokers(): void
    {
        if ($this->lokers()->exists()) {
            return;
        }

        $koloms = range('A', chr(ord('A') + $this->jumlah_kolom - 1));

        foreach ($koloms as $kolom) {
            for ($baris = 1; $baris <= $this->jumlah_baris_per_kolom; $baris++) {
                Loker::create([
                    'lemari_id'  => $this->id,
                    'kode_loker' => "{$this->kode_lemari}.{$kolom}." . str_pad($baris, 4, '0', STR_PAD_LEFT),
                    'kolom'      => $kolom,
                    'baris'      => $baris,
                    'kapasitas'  => 350,
                    'terisi'     => 0,
                    'status'     => 'tersedia',
                ]);
            }
        }
    }

    /* ================= SYNC STATUS ================= */

    public function syncStatus(): void
    {
        if ($this->status === 'nonaktif') {
            return;
        }

        $this->status = $this->jumlahLokerTersedia() > 0
            ? 'aktif'
            : 'penuh';

        $this->save();
    }

    /* ================= DISPLAY ================= */

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
        return ucfirst($this->status);
    }
}
