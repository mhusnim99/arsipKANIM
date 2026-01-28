<?php

namespace App\Services;

use App\Models\Lemari;
use App\Models\Loker;

class LokerAllocator
{
    public static function pick(): ?Loker
    {
        // 1. Ambil lemari aktif TERLAMA
        $lemari = Lemari::where('status', 'aktif')
            ->orderBy('created_at')
            ->lockForUpdate()
            ->get()
            ->first(function ($lemari) {
                return $lemari->lokers()
                    ->where('status', 'aktif')
                    ->whereRaw(
                        '(select count(*) from arsips where arsips.loker_id = lokers.id) < kapasitas'
                    )
                    ->exists();
            });

        if (! $lemari) {
            return null;
        }

        // 2. Ambil loker A1 → A2 → B1 → dst
        return $lemari->lokers()
            ->where('status', 'aktif')
            ->whereRaw(
                '(select count(*) from arsips where arsips.loker_id = lokers.id) < kapasitas'
            )
            ->orderBy('kolom')
            ->orderBy('baris')
            ->lockForUpdate()
            ->first();
    }
}
