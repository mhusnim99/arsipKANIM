<?php

namespace App\Http\Controllers;

use App\Models\PengirimanBerkas;
use App\Models\Arsip;
use App\Models\Lemari;
use App\Models\Loker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Services\LokerAllocator;

class PenerimaanArsipController extends Controller
{
    /**
     * Menampilkan daftar pengiriman menunggu penerimaan
     */


    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Akses hanya untuk Petugas Arsip');
        }

        // QUERY UTAMA
        $query = PengirimanBerkas::with('petugasPengirim')
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'desc');

        // SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_permohonan', 'like', "%{$search}%")
                    ->orWhere('asal_berkas', 'like', "%{$search}%")
                    ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        // FILTER TANGGAL
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_kirim', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_kirim', '<=', $request->tanggal_selesai);
        }

        // DATA TABEL
        $pengirimanBerkas = $query->paginate(10);

        // STATISTIK
        $totalMenunggu = PengirimanBerkas::where('status', 'menunggu')->count();
        $totalDiterima = PengirimanBerkas::where('status', 'diterima')->count();
        $totalDitolak  = PengirimanBerkas::where('status', 'ditolak')->count();

        // MASTER DATA
        $lemaris = Lemari::with('lokers')
            ->where('status', 'aktif')
            ->get();

        $lokers = Loker::where('status', 'aktif')->get();

        return view('admin.penerimaan-arsip', compact(
            'pengirimanBerkas',
            'totalMenunggu',
            'totalDiterima',
            'totalDitolak',
            'lemaris'
        ));
    }


    // tampilkan form (modal)
    public function formTerima($id)
    {
        $pengiriman = PengirimanBerkas::findOrFail($id);
        $lemaris = Lemari::where('status', 'aktif')->get();

        return view('penerimaan-arsip.modal-terima', compact('pengiriman', 'lemaris'));
    }
    public function terima(Request $request, $id)
    {
        $request->validate([
            'lemari_id' => 'nullable|exists:lemaris,id',
            'loker_id'  => 'nullable|exists:lokers,id',
        ]);

        DB::transaction(function () use ($request, $id) {

            $pengiriman = PengirimanBerkas::lockForUpdate()->findOrFail($id);
            // $loker = $request->filled('loker_id')
            //     ? Loker::lockForUpdate()->findOrFail($request->loker_id)
            //     : LokerAllocator::pick();

            // if (! $loker) {
            //     abort(422, 'Tidak ada loker tersedia');
            // }
            $loker = LokerAllocator::pick();

            if (! $loker) {
                abort(422, 'Tidak ada loker tersedia di seluruh lemari');
            }

            $nomorArsip = $loker->generateNomorArsip();

            $arsip = Arsip::create([
                'pengiriman_berkas_id' => $pengiriman->id,
                'kode_permohonan'      => $pengiriman->kode_permohonan,
                'nomor_arsip'          => $nomorArsip,
                'tanggal_masuk'        => now(),
                'asal_berkas'          => $pengiriman->asal_berkas,
                'lemari_id'            => $loker->lemari_id,
                'loker_id'             => $loker->id,
                'status'               => 'tersimpan',
                'diterima_oleh'        => Auth::id(),
            ]);

            $loker->refresh();        // ⬅ WAJIB
            $loker->syncStatus();

            $loker->lemari->refresh(); // ⬅ WAJIB
            $loker->lemari->syncStatus();

            $pengiriman->update([
                'status'        => 'diterima',
                'arsip_id'      => $arsip->id,
                'nomor_arsip'   => $nomorArsip,
                'diterima_pada' => now(),
                'diterima_oleh' => Auth::id(),
            ]);
        });

        return back()->with('success', 'Arsip berhasil diterima');
    }
    /**
     * Proses menolak arsip
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required'
        ]);

        $arsip = Arsip::where('pengiriman_berkas_id', $id)->firstOrFail();
        $arsip->delete(); // arsip dihapus

        PengirimanBerkas::where('id', $id)->update([
            'status' => 'ditolak'
        ]);
    }

    /**
     * Get loker berdasarkan lemari (AJAX)
     */
    public function getLokersByLemari(Lemari $lemari)
    {
        $lokers = $lemari->lokers()
            ->whereIn('status', ['aktif']) // ❗ hanya yang bisa diisi
            ->whereColumn(
                'kapasitas',
                '>',
                DB::raw('(select count(*) from arsips where arsips.loker_id = lokers.id)')
            )
            ->orderBy('kolom')
            ->orderBy('baris')
            ->get();

        return response()->json(
            $lokers->map(fn($loker) => [
                'id' => $loker->id,
                'kode_loker' => $loker->kode_loker,
            ])
        );
    }

    /**
     * Menampilkan detail pengiriman
     */
    public function show($id)
    {
        $pengiriman = PengirimanBerkas::with('petugasPengirim')->findOrFail($id);

        // PERBAIKAN: Pastikan response dalam format JSON yang benar
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pengiriman->id,
                'kode_permohonan' => $pengiriman->kode_permohonan,
                'asal_berkas' => $pengiriman->asal_berkas,
                'tanggal_kirim' => $pengiriman->tanggal_kirim,
                'catatan' => $pengiriman->catatan,
                'created_at' => $pengiriman->created_at,
                'petugas_pengirim' => $pengiriman->petugasPengirim,
                'status' => $pengiriman->status
            ]
        ]);
    }
}
