<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Lemari;
use App\Models\Loker;
use App\Models\PengirimanBerkas;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ================= STATISTIK ARSIP =================

        $totalArsip = Arsip::count();

        $menunggu = PengirimanBerkas::where('status', 'menunggu')->count();
        $diterima = Arsip::where('status', 'tersimpan')->count();
        $ditolak  = Arsip::where('status', 'musnah')->count();

        // ================= STATISTIK LEMARI & LOKER =================

        $totalLemari = Lemari::count();
        $totalLoker  = Loker::count();
        $totalKapasitas = Loker::sum('kapasitas');

        // ================= KAPASITAS =================

        $totalTerisi = Arsip::count();

        $sisaKapasitas = $totalKapasitas - $totalTerisi;

        $persenPemakaian = $totalKapasitas > 0
            ? round(($totalTerisi / $totalKapasitas) * 100, 2)
            : 0;

        // ================= GRAFIK BULANAN =================

        $grafik = Arsip::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
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

        $slotPenuh = Loker::with('lemari')
            ->get()
            ->filter(function ($loker) {
                return $loker->jumlahArsip() >= $loker->kapasitas;
            })
            ->take(10)
            ->map(function ($loker) {
                return (object) [
                    'lemari'     => $loker->lemari->kode_lemari,
                    'loker'      => $loker->kode_loker,
                    'terisi'     => $loker->jumlahArsip(),
                    'kapasitas'  => $loker->kapasitas,
                ];
            });

        return view('admin.dashboard', compact(
            'totalArsip',
            'menunggu',
            'diterima',
            'ditolak',
            'totalLemari',
            'totalLoker',
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