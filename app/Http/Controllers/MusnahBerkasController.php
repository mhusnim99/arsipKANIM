<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class MusnahBerkasController extends Controller
{
    /**
     * INDEX
     * - Kalau tidak ada parameter tahun → tampil folder
     * - Kalau ada tahun → tampil isi folder
     */
    public function index(Request $request)
    {
        // CEK kalau buka folder tahun
        if ($request->tahun) {
            return $this->showByYear($request->tahun);
        }

        // Ambil list tahun dari data arsip
       $years = Arsip::selectRaw('YEAR(created_at) as tahun')
    ->whereYear('created_at', '<', Carbon::now()->year)
    ->distinct()
    ->orderByDesc('tahun')
    ->pluck('tahun');

        return view('admin.musnah-berkas', compact('years'));
    }

    /**
     * TAMPILKAN DATA BERDASARKAN TAHUN
     */
    public function showByYear($tahun)
{
    $arsips = Arsip::with('lemari')
        ->whereYear('created_at', $tahun)

        // 🔥 INI KUNCI NYA
        ->whereYear('created_at', '<', Carbon::now()->year)

        ->orderByDesc('created_at')
        ->paginate(10);

    return view('admin.musnah-berkas', [
        'arsips' => $arsips,
        'tahun' => $tahun
    ]);
}

    /**
     * DOWNLOAD PDF
     */
    public function downloadPdf($id)
    {
        $arsip = Arsip::with('lemari')->findOrFail($id);

        $pdf = PDF::loadView('admin.pdf.musnah', compact('arsip'));

        return $pdf->download('arsip-' . $arsip->id . '.pdf');
    }

    public function showPdf($id)
{
    $arsip = Arsip::with('lemari')->findOrFail($id);

    return view('admin.pdf.musnah', compact('arsip'));
}
    /**
     * HAPUS DATA
     * - Bisa hapus per item (ids[])
     * - Bisa hapus per tahun (folder)
     */
    public function bulkDelete(Request $request)
    {
        // HAPUS PER ITEM
        if ($request->ids) {
            Arsip::whereIn('id', $request->ids)->delete();

            return back()->with('success', 'Data berhasil dihapus');
        }

        // HAPUS PER TAHUN (FOLDER)
        if ($request->tahun) {
            Arsip::whereYear('created_at', $request->tahun)->delete();

            return back()->with('success', 'Folder berhasil dihapus');
        }

        return back()->with('error', 'Tidak ada data yang dipilih');
    }
}
