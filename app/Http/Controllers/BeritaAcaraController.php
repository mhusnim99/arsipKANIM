<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use App\Models\PengirimanBerkas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Generate berita acara otomatis
     * mengambil semua pengiriman status menunggu
     */
    public function generate()
    {
        $user = Auth::user();

        $pengiriman = PengirimanBerkas::where('petugas_pengirim_id', $user->id)
            ->where('status', 'menunggu')
            ->whereNull('berita_acara_id')
            ->get();

        if ($pengiriman->count() == 0) {
            return back()->with('error', 'Tidak ada data pengiriman yang bisa dibuat berita acara.');
        }

        DB::beginTransaction();

        try {

            $nomor = 'BA/' . date('Y') . '/' . str_pad(BeritaAcara::count() + 1, 4, '0', STR_PAD_LEFT);

            $beritaAcara = BeritaAcara::create([
                'nomor_berita_acara' => $nomor,
                'petugas_pengirim_id' => $user->id,
                'tanggal_dibuat' => now(),
                'jumlah_arsip' => $pengiriman->count(),
            ]);

            foreach ($pengiriman as $item) {
                $item->update([
                    'berita_acara_id' => $beritaAcara->id
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', 'Gagal membuat berita acara');
        }

        $pdf = Pdf::loadView('user.berita-acara.pdf', [
            'beritaAcara' => $beritaAcara,
            'pengiriman' => $pengiriman
        ]);

        $fileName = 'berita-acara-' . str_replace('/', '-', $nomor) . '.pdf';

        return $pdf->stream($fileName);
    }


    /**
     * Detail berita acara
     */
    public function show($id)
    {
        $beritaAcara = BeritaAcara::with('pengirimanBerkas')
            ->where('petugas_pengirim_id', Auth::id())
            ->findOrFail($id);

        return view('user.berita-acara.show', compact('beritaAcara'));
    }


    /**
     * Generate dan download PDF berita acara
     */
    public function generatePdf($id)
    {
        $beritaAcara = BeritaAcara::with('pengirimanBerkas')
            ->where('petugas_pengirim_id', Auth::id())
            ->findOrFail($id);

        $pdf = Pdf::loadView('user.berita-acara.pdf', [
            'beritaAcara' => $beritaAcara
        ]);

        $fileName = 'berita-acara-' . $beritaAcara->nomor_berita_acara . '.pdf';

        $path = 'berita-acara/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());

        $beritaAcara->update([
            'file_path' => $path
        ]);

        return response()->download(storage_path('app/public/' . $path));
    }
}
