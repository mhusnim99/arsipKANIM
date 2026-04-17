<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BeritaAcaraController extends Controller
{

    /**
     * Menampilkan daftar berita acara milik petugas
     */
    public function index()
    {
        $beritaAcara = BeritaAcara::where('petugas_pengirim_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.berita-acara.index', compact('beritaAcara'));
    }


    /**
     * Generate berita acara (VERSI BARU)
     * Tidak mengambil data pengiriman
     * Langsung generate PDF
     */
    public function generate()
    {
        $user = Auth::user();

        // Generate nomor otomatis
        $nomor = 'BA/' . date('Y') . '/' . str_pad(BeritaAcara::count() + 1, 4, '0', STR_PAD_LEFT);

        // Simpan ke database
        $beritaAcara = BeritaAcara::create([
            'nomor_berita_acara' => $nomor,
            'petugas_pengirim_id' => $user->id,
            'tanggal_dibuat' => now(),
            'jumlah_arsip' => 0 // default, bisa dikembangkan nanti
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('user.berita-acara.pdf', [
            'beritaAcara' => $beritaAcara
        ]);

        $fileName = 'berita-acara-' . str_replace('/', '-', $nomor) . '.pdf';

        return $pdf->download($fileName);
    }


    /**
     * Detail berita acara (opsional, tanpa pengiriman)
     */
    public function show($id)
    {
        $beritaAcara = BeritaAcara::where('petugas_pengirim_id', Auth::id())
            ->findOrFail($id);

        return view('user.berita-acara.show', compact('beritaAcara'));
    }


    /**
     * Download ulang PDF berita acara
     */
    public function generatePdf($id)
    {
        $beritaAcara = BeritaAcara::where('petugas_pengirim_id', Auth::id())
            ->findOrFail($id);

        $pdf = Pdf::loadView('user.berita-acara.pdf', [
            'beritaAcara' => $beritaAcara
        ]);

        $fileName = 'berita-acara-' . str_replace('/', '-', $beritaAcara->nomor_berita_acara) . '.pdf';

        $path = 'berita-acara/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());

        $beritaAcara->update([
            'file_path' => $path
        ]);

        return response()->download(storage_path('app/public/' . $path));
    }
}