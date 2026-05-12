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
            ->join('lemaris', 'lokers.lemari_id', '=', 'lemaris.id')
            ->orderByRaw("CAST(SUBSTRING(lemaris.kode_lemari, 2) AS UNSIGNED) ASC")
            ->orderBy('lokers.kolom', 'ASC')
            ->orderBy('lokers.baris', 'ASC')
            ->select('lokers.*')
            ->get()

            ->first(function ($loker) {
                return $loker->arsips()->count() < $loker->kapasitas;
            });
    }
}
