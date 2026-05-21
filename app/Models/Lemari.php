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
        'kapasitas_default_loker',
        'jumlah_loker',
    ];

    protected $casts = [
        'jumlah_kolom' => 'integer',
        'jumlah_baris_per_kolom' => 'integer',
        'kapasitas_default_loker' => 'integer',
        'jumlah_loker' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($lemari) {
            $existingNumbers = self::pluck('kode_lemari')
                ->map(function ($kode) {
                    return (int) str_replace('L', '', $kode);
                })
                ->sort()
                ->values()
                ->toArray();
            $nextNumber = 1;

            foreach ($existingNumbers as $number) {

                if ($number == $nextNumber) {
                    $nextNumber++;
                } else {
                    break;
                }
            }
            $lemari->kode_lemari = 'L' . $nextNumber;
        });
    }

    //Relasi

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
        return $this->lokers()->where('status', 'aktif')->count();
    }

    /* ================= GENERATE LOKER ================= */

    // public function generateLokers(): void
    // {
    //     if ($this->lokers()->exists()) {
    //         return;
    //     }

    //     $koloms = range('A', chr(ord('A') + $this->jumlah_kolom - 1));

    //     foreach ($koloms as $kolom) {
    //         for ($baris = 1; $baris <= $this->jumlah_baris_per_kolom; $baris++) {

    //             $kodeLoker = "{$kolom}{$baris}";

    //             Loker::create([
    //                 'lemari_id'  => $this->id,
    //                 'kode_loker' => $kodeLoker,
    //                 'kolom'      => $kolom,
    //                 'baris'      => $baris,
    //                 'kapasitas' => $this->kapasitas_default_loker,
    //                 'status'     => 'aktif',
    //             ]);
    //         }
    //     }
    // }
    public function generateLokers(): void
    {
        if ($this->lokers()->exists()) {
            return;
        }

        $jumlahPerKolom = 10;

        for ($i = 1; $i <= $this->jumlah_loker; $i++) {

            $kolomIndex = floor(($i - 1) / $jumlahPerKolom);
            $kolom = chr(65 + $kolomIndex);
            $baris = (($i - 1) % $jumlahPerKolom) + 1;
            $kodeLoker = "{$kolom}{$baris}";

            Loker::create([
                'lemari_id'  => $this->id,
                'kode_loker' => $kodeLoker,
                'kolom'      => $kolom,
                'baris'      => $baris,
                'kapasitas'  => $this->kapasitas_default_loker,
                'status'     => 'aktif',
            ]);
        }
    }

    /* ================= SYNC STATUS ================= */

    public function syncStatus(): void
    { {
            $total = $this->lokers()->count();

            $penuh = $this->lokers()
                ->where('status', 'penuh')
                ->count();

            $this->status = ($penuh === $total && $total > 0)
                ? 'penuh'
                : 'aktif';

            $this->save();
        }
    }

    /* ================= DISPLAY ================= */

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'aktif'     => 'success',
            'penuh'     => 'warning',
            default     => 'secondary',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function jumlahLokerPenuh(): int
    {
        return $this->lokers
            ->filter(function ($loker) {
                return $loker->arsips->count() >= $loker->kapasitas;
            })
            ->count();
    }
}
