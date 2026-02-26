<?php

namespace App\Http\Controllers;

use App\Models\PengirimanBerkas;
use App\Models\Arsip;
use App\Models\Lemari;
use App\Models\Loker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\LokerAllocator;

class PenerimaanArsipController extends Controller
{
    /**
     * Daftar pengiriman menunggu penerimaan
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Akses hanya untuk Petugas Arsip');
        }

        $query = PengirimanBerkas::with('petugasPengirim')
            ->where('status', 'menunggu')
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_permohonan', 'like', "%{$search}%")
                    ->orWhere('asal_berkas', 'like', "%{$search}%")
                    ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_kirim', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_kirim', '<=', $request->tanggal_selesai);
        }

        $pengirimanBerkas = $query->paginate(10);

        $totalMenunggu = PengirimanBerkas::where('status', 'menunggu')->count();
        $totalDiterima = PengirimanBerkas::where('status', 'diterima')->count();
        $totalDitolak  = PengirimanBerkas::where('status', 'ditolak')->count();

        $lemaris = Lemari::with(['lokers' => function ($q) {
            $q->where('status', 'aktif')
                ->orderBy('kolom')
                ->orderBy('baris');
        }])
            ->where('status', 'aktif')
            ->get();

        return view('admin.penerimaan-arsip', compact(
            'pengirimanBerkas',
            'totalMenunggu',
            'totalDiterima',
            'totalDitolak',
            'lemaris'
        ));
    }

    /**
     * Proses menerima arsip
     */
    public function terima($id)
    {
        $result = DB::transaction(function () use ($id) {

            $pengiriman = PengirimanBerkas::lockForUpdate()->findOrFail($id);

            if ($pengiriman->status !== 'menunggu') {
                throw new \Exception('Berkas sudah diproses');
            }

            // 🔹 Ambil snapshot SIMKIM
            $snapshot = $pengiriman->simkim_snapshot;

            if (!$snapshot || !isset($snapshot['permohonan'])) {
                throw new \Exception('Snapshot SIMKIM tidak ditemukan');
            }

            $permohonan = $snapshot['permohonan'];

            // 🔹 Cari loker aktif yang masih punya slot
            $loker = Loker::lockForUpdate()
                ->where('status', 'aktif')
                ->get()
                ->first(function ($l) {
                    return $l->jumlahArsip() < $l->kapasitas;
                });

            if (!$loker) {
                throw new \Exception('Tidak ada loker aktif');
            }

            // 🔹 Generate nomor arsip otomatis
            $nomorUrut = $loker->jumlahArsip() + 1;
            $nomorFormatted = str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);
            $nomorArsip = "{$loker->kode_loker}.{$nomorFormatted}";

            // 🔹 Simpan arsip (sekarang pakai data snapshot)
            $arsip = Arsip::create([
                'pengiriman_berkas_id' => $pengiriman->id,
                'kode_permohonan'      => $permohonan['nopermohonan'],
                'nomor_arsip'          => $nomorArsip,
                'tanggal_masuk'        => now(),
                'asal_berkas'          => $pengiriman->asal_berkas,
                'lemari_id'            => $loker->lemari_id,
                'loker_id'             => $loker->id,
                'status'               => 'tersimpan',
                'diterima_oleh'        => Auth::id(),

                // OPTIONAL: simpan snapshot lengkap
                'keterangan'           => json_encode($snapshot),
            ]);

            // 🔹 Sync status loker & lemari
            $loker->syncStatus();
            $loker->lemari->syncStatus();

            // 🔹 Update pengiriman
            $pengiriman->update([
                'status'      => 'diterima',
                'arsip_id'    => $arsip->id,
                'nomor_arsip' => $nomorArsip,
            ]);

            return [
                'kode_permohonan' => $permohonan['nopermohonan'],
                'lokasi' => 'Lemari ' . $loker->lemari->kode_lemari .
                    ' / Loker ' . $loker->kode_loker,
                'status' => 'Tersimpan',
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }

    /**
     * Tolak arsip
     */
    public function tolak(Request $request, $id)
    {
        $pengiriman = PengirimanBerkas::findOrFail($id);

        if ($pengiriman->status !== 'menunggu') {
            return back()->with('error', 'Data sudah diproses.');
        }

        $pengiriman->update([
            'status' => 'ditolak'
        ]);

        return back()->with('success', 'Pengiriman ditolak.');
    }

    /**
     * Ambil loker berdasarkan lemari (AJAX)
     */
    public function getLokersByLemari(Lemari $lemari)
    {
        $lokers = $lemari->lokers()
            ->where('status', 'aktif')
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
                'id'         => $loker->id,
                'kode_loker' => $loker->kode_loker,
            ])
        );
    }

    /**
     * Detail pengiriman
     */
    public function show($id)
    {
        $pengiriman = PengirimanBerkas::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'kode_permohonan' => $pengiriman->kode_permohonan,
                'asal_berkas'     => $pengiriman->asal_berkas,
                'status'          => $pengiriman->status,
                'simkim'          => $pengiriman->simkim_snapshot,
            ]
        ]);
    }
}
