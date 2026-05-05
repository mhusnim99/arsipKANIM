<?php

namespace App\Services;

use App\Models\Arsip;

class ArsipCsvExporter
{
    public static function stream(int $tahun, $handle)
    {
        // HEADER
        fputcsv($handle, [
            'Nomor Arsip',
            'Kode Permohonan',
            'Nama',
            'Lemari',
            'Loker',
            'Tanggal Masuk',
            'Status',
        ]);

        Arsip::with(['lemari', 'loker'])
            ->siapMusnah()
            ->whereYear('created_at', $tahun)
            ->orderBy('id')
            ->chunk(1000, function ($arsips) use ($handle) {

                foreach ($arsips as $arsip) {
                    fputcsv($handle, [
                        $arsip->nomor_arsip,
                        $arsip->kode_permohonan,
                        $arsip->nama_lengkap,
                        $arsip->lemari->kode_lemari ?? '-',
                        $arsip->loker->kode_loker ?? '-',
                        optional($arsip->created_at)->format('d-m-Y'),
                        $arsip->status,
                    ]);
                }
            });
    }
}