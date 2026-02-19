<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ================= STATISTIK ARSIP =================

        $totalArsip = DB::table('arsips')->count();

        // SESUAI STRUKTUR STATUS DI DATABASE
        $pending  = DB::table('arsips')->whereNull('status')->count();
        $diterima = DB::table('arsips')->where('status', 'tersimpan')->count();
        $ditolak  = DB::table('arsips')->where('status', 'musnah')->count();

        // ================= STATISTIK LEMARI & LOKER =================

        $totalLemari = DB::table('lemaris')->count();
        $totalLoker  = DB::table('lokers')->count();
        $totalSlot   = DB::table('lokers')->sum('kapasitas_maksimal');

        // ================= KAPASITAS =================

        $totalKapasitas = DB::table('lokers')->sum('kapasitas_maksimal');
        $totalTerisi    = DB::table('lokers')->sum('terisi');

        $sisaKapasitas = $totalKapasitas - $totalTerisi;

        $persenPemakaian = $totalKapasitas > 0
            ? round(($totalTerisi / $totalKapasitas) * 100, 2)
            : 0;

        // ================= GRAFIK BULANAN =================

        $grafik = DB::table('arsips')
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $bulan  = [];
        $jumlah = [];

        foreach ($grafik as $g) {
            $bulan[]  = date('M', mktime(0, 0, 0, $g->bulan, 1));
            $jumlah[] = $g->total;
        }

        // ================= MONITORING LOKER PENUH =================

        $slotPenuh = DB::table('lokers')
            ->join('lemaris', 'lokers.lemari_id', '=', 'lemaris.id')
            ->whereColumn('lokers.terisi', '>=', 'lokers.kapasitas_maksimal')
            ->select(
                'lemaris.kode_lemari as lemari',
                'lokers.kode_loker as loker',
                'lokers.terisi',
                'lokers.kapasitas_maksimal as kapasitas'
            )
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalArsip',
            'pending',
            'diterima',
            'ditolak',
            'totalLemari',
            'totalLoker',
            'totalSlot',
            'totalKapasitas',
            'totalTerisi',
            'sisaKapasitas',
            'persenPemakaian',
            'bulan',
            'jumlah',
            'slotPenuh'
        ));
    }
}
