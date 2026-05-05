<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ArsipCsvExporter;

class MusnahBerkasController extends Controller
{
    /**
     * ✅ LIST FOLDER + JUMLAH ARSIP
     * (OPTIMAL - TANPA N+1 QUERY)
     */
    public function index()
    {
        $years = Arsip::siapMusnah()
            ->selectRaw('YEAR(created_at) as tahun, COUNT(*) as total')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->get();

        return view('admin.musnah-berkas', compact('years'));
    }

    public function downloadCsv(int $tahun)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $fileName = "arsip-{$tahun}.csv";

        return response()->streamDownload(function () use ($tahun) {

            while (ob_get_level()) {
                ob_end_clean();
            }

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Nomor Arsip',
                'Kode Permohonan',
                'Nama',
                'Lemari',
                'Loker',
                'Tanggal Masuk',
                'Status',
            ]);

            Arsip::query()
                ->join('lokers', 'lokers.id', '=', 'arsips.loker_id')
                ->join('lemaris', 'lemaris.id', '=', 'lokers.lemari_id')
                ->select(
                    'arsips.nomor_arsip',
                    'arsips.kode_permohonan',
                    'arsips.nama_lengkap',
                    'lemaris.kode_lemari',
                    'lokers.kode_loker',
                    'arsips.created_at',
                    'arsips.status'
                )
                ->whereYear('arsips.created_at', $tahun)
                ->where('arsips.created_at', '<=', now()->subYear())
                ->orderBy('arsips.id')
                ->chunk(1000, function ($rows) use ($handle) {

                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $row->nomor_arsip,
                            $row->kode_permohonan,
                            $row->nama_lengkap,
                            $row->kode_lemari,
                            $row->kode_loker,
                            \Carbon\Carbon::parse($row->created_at)->format('d-m-Y'),
                            $row->status,
                        ]);
                    }

                    flush(); // 🔥 penting
                });

            fclose($handle);
        }, $fileName);
    }

    /**
     * ✅ HAPUS PER TAHUN (SCALABLE)
     * - pakai chunk (ANTI MEMORY OVERLOAD)
     * - auto reset loker & lemari
     */
    public function destroy(int $tahun)
    {
        DB::transaction(function () use ($tahun) {

            Arsip::with('loker')
                ->siapMusnah()
                ->whereYear('created_at', $tahun)
                ->orderBy('id') // WAJIB untuk chunkById
                ->chunkById(1000, function ($arsips) {

                    // 🔥 ambil loker unik
                    $lokers = $arsips->pluck('loker')->filter()->unique('id');

                    // 🔥 delete batch
                    Arsip::whereIn('id', $arsips->pluck('id'))->delete();

                    // 🔥 sync ulang
                    foreach ($lokers as $loker) {
                        $loker->syncStatus();
                        $loker->lemari->syncStatus();
                    }
                });
        });

        return back()->with('success', 'Arsip berhasil dimusnahkan');
    }
}
