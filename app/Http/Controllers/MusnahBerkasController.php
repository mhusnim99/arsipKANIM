<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ArsipCsvExporter;

class MusnahBerkasController extends Controller
{

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

            // UTF-8 BOM agar Excel membaca karakter dengan benar
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // HEADER CSV
            fputcsv($handle, [
                'Nomor Arsip',
                'Kode Permohonan',
                'Nama',
                'Lemari',
                'Loker',
                'Tanggal Masuk',
                'Status',
            ], ';');

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
                ->where('arsips.status', 'tersimpan')
                ->whereYear('arsips.created_at', $tahun)
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
                            ucfirst($row->status),
                        ], ';');
                    }

                    flush();
                });

            fclose($handle);
        }, $fileName);
    }

    public function destroy(int $tahun)
    {
        DB::transaction(function () use ($tahun) {

            Arsip::with('loker')
                ->siapMusnah()
                ->whereYear('created_at', $tahun)
                ->orderBy('id')
                ->chunkById(1000, function ($arsips) {

                    $lokers = $arsips->pluck('loker')->filter()->unique('id');

                    Arsip::whereIn('id', $arsips->pluck('id'))->delete();


                    foreach ($lokers as $loker) {
                        $loker->syncStatus();
                        $loker->lemari->syncStatus();
                    }
                });
        });

        return back()->with('success', 'Arsip berhasil dimusnahkan');
    }
}
