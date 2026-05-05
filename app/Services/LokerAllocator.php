<?php

namespace App\Services;

use App\Models\Loker;
use Illuminate\Support\Facades\DB;

class LokerAllocator
{
    public static function getAvailableLoker()
    {
        return \App\Models\Loker::whereHas('lemari', function ($q) {
            $q->where('status', 'aktif');
        })
            ->with('lemari')

            // 🔥 URUTAN LEMARI (L1, L2, L3)
            ->join('lemaris', 'lokers.lemari_id', '=', 'lemaris.id')
            ->orderByRaw("CAST(SUBSTRING(lemaris.kode_lemari, 2) AS UNSIGNED) ASC")

            // 🔥 URUTAN LOKER (A → B → C)
            ->orderBy('lokers.kolom', 'ASC')

            // 🔥 URUTAN BARIS (1 → 10)
            ->orderBy('lokers.baris', 'ASC')

            ->select('lokers.*') // penting karena join
            ->get()

            // 🔥 FILTER: ambil yang belum penuh
            ->first(function ($loker) {
                return $loker->arsips()->count() < $loker->kapasitas;
            });
    }
}
