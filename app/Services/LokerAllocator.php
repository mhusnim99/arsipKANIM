<?php

namespace App\Services;

use App\Models\Loker;

class LokerAllocator
{
    public static function getAvailableLoker()
    {
        return Loker::withCount('arsips')
            ->where('status', 'aktif')
            ->orderBy('kolom')   
            ->orderBy('baris')   
            ->get()
            ->first(function ($loker) {
                return $loker->arsips_count < $loker->kapasitas;
            });
    }
}