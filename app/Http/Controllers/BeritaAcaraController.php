<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BeritaAcaraController extends Controller
{
    public function index()
    {
        $beritaAcara = BeritaAcara::where('petugas_pengirim_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.berita-acara.index', compact('beritaAcara'));
    }

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
            'jumlah_arsip' => 0
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('user.berita-acara.pdf', [
            'beritaAcara' => $beritaAcara
        ]);

        $fileName = 'berita-acara-' . str_replace('/', '-', $nomor) . '.pdf';

        return $pdf->download($fileName);
    }

    public function show($id)
    {
        $beritaAcara = BeritaAcara::where('petugas_pengirim_id', Auth::id())
            ->findOrFail($id);

        return view('user.berita-acara.show', compact('beritaAcara'));
    }

    public function generatePdf($id)
    {
        $beritaAcara = BeritaAcara::where(
            'petugas_pengirim_id',
            Auth::id()
        )->findOrFail($id);

        $beritaAcara->update([
            'tanggal_dibuat' => now()
        ]);

        $beritaAcara->refresh();

        $pdf = Pdf::loadView('user.berita-acara.pdf', [
            'beritaAcara' => $beritaAcara
        ]);

        $fileName = 'berita-acara-' .
            str_replace('/', '-', $beritaAcara->nomor_berita_acara) .
            '.pdf';

        return $pdf->download($fileName);
    }
}
