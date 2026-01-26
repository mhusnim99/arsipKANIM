<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Loker;

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
        'kapasitas_total' => 'integer',
        'penuh' => 'integer'
    ];

    /**
     * Relasi ke loker
     */
    public function lokers()
    {
        return $this->hasMany(Loker::class);
    }

    public function arsips()
    {
        return $this->hasManyThrough(Arsip::class, Loker::class);
    }

    public function getJumlahLokerTerisiAttribute(): int
    {
        return $this->lokers()
            ->whereIn('status', ['aktif', 'penuh'])
            ->count();
    }
    /**
     * Scope untuk lemari aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk lemari dengan kapasitas tersedia
     */
    public function scopeTersedia($query)
    {
        return $query->where('status', 'aktif')
            ->whereColumn('penuh', '<', 'kapasitas_total');
    }

    /**
     * Hitung persentase penuh
     */
    public function getPersentaseTerisiAttribute()
    {
        if ($this->kapasitas_total == 0) return 0;
        return round(($this->penuh / $this->kapasitas_total) * 100, 2);
    }

    /**
     * Hitung kapasitas tersisa
     */
    public function getKapasitasTersisaAttribute()
    {
        return $this->kapasitas_total - $this->penuh;
    }

    /**
     * Generate loker otomatis
     */
    public function generateLokers(): void
    {
        // 🔒 GUARD: jangan generate ulang
        if ($this->lokers()->exists()) {
            return;
        }

        $koloms = range('A', chr(ord('A') + $this->jumlah_kolom - 1));

        foreach ($koloms as $kolom) {
            for ($baris = 1; $baris <= $this->jumlah_baris_per_kolom; $baris++) {
                Loker::create([
                    'lemari_id'  => $this->id,
                    'kode_loker' => sprintf(
                        '%s.%s.%04d',
                        $this->kode_lemari,
                        $kolom,
                        $baris
                    ),
                    'kolom'      => $kolom,
                    'baris'      => $baris,
                    'kapasitas'  => 1,
                    'status'     => 'aktif',
                ]);
            }
        }
    }


    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'aktif' => 'success',
            'nonaktif' => 'secondary',
            'penuh' => 'warning'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $texts = [
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            'penuh' => 'Penuh'
        ];

        return $texts[$this->status] ?? $this->status;
    }
    public function syncStatus(): void
    {
        if ($this->status === 'nonaktif') {
            return;
        }

        $this->status = $this->jumlahArsip() >= $this->kapasitasTotalArsip()
            ? 'penuh'
            : 'aktif';

        $this->save();
    }
    public function kapasitasTotalArsip(): int
    {
        return $this->lokers()->sum('kapasitas');
    }

    public function jumlahArsip(): int
    {
        return $this->arsips()->count();
    }
}
