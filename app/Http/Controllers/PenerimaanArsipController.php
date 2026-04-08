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

        if ($user->role !== 'admin' && $user->role !== 'petugas_arsip') {
            abort(403, 'Akses hanya untuk Petugas Arsip');
        }

        $query = PengirimanBerkas::with('petugasPengirim')
            ->where('status', 'menunggu')
            ->orderByDesc('created_at');

        // 🔍 SEARCH (tetap dipakai)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_permohonan', 'like', "%{$search}%")
                    ->orWhere('asal_berkas', 'like', "%{$search}%")
                    ->orWhere('catatan', 'like', "%{$search}%");
            });
        }

        // 🔥 FILTER PETUGAS (FINAL)
        if ($request->filled('petugas')) {
            $query->where('petugas_pengirim_id', $request->petugas);
        }

     $pengirimanBerkas = $query->latest()->limit(100)->get();

        // 📊 Statistik
        $totalMenunggu = PengirimanBerkas::where('status', 'menunggu')->count();
        $totalDiterima = PengirimanBerkas::where('status', 'diterima')->count();
        $totalDitolak  = PengirimanBerkas::where('status', 'ditolak')->count();

        // 📦 Lemari
        $lemaris = Lemari::with(['lokers' => function ($q) {
            $q->where('status', 'aktif')
                ->orderBy('kolom')
                ->orderBy('baris');
        }])
            ->where('status', 'aktif')
            ->get();

        // 🔥 LIST PETUGAS (WAJIB)
        $petugasList = \App\Models\User::where('role', 'user')->get();

        return view('admin.penerimaan-arsip', compact(
            'pengirimanBerkas',
            'totalMenunggu',
            'totalDiterima',
            'totalDitolak',
            'lemaris',
            'petugasList'
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
        $lastArsip = Arsip::where('loker_id', $loker->id)
    ->orderByDesc('id')
    ->first();

$lastArsip = Arsip::where('loker_id', $loker->id)
    ->orderByDesc('id')
    ->lockForUpdate()
    ->first();

if ($lastArsip) {
    $lastNumber = (int) substr($lastArsip->nomor_arsip, -4);
    $nomorUrut = $lastNumber + 1;
} else {
    $nomorUrut = 1;
}
        $nomorFormatted = str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);
        $nomorArsip = "{$loker->lemari->kode_lemari}.{$loker->kode_loker}.{$nomorFormatted}";

        // 🔹 FIX: kasih fallback biar gak NULL
        $namaLengkap = $permohonan['nama_lengkap'] ?? 'Tidak diketahui';
        $nomorPaspor = $permohonan['nopaspor'] ?? '-';

        // 🔹 Simpan arsip
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

            // 🔹 DATA (SUDAH AMAN)
            'nama_lengkap'         => $namaLengkap,
            'nomor_paspor'         => $nomorPaspor,
            'tanggal_permohonan'   => $permohonan['tanggal_permohonan'] ?? null,
            'status_proses'        => $permohonan['alurterakhir'] ?? null,

            'keterangan'           => null,
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




    public function preview($id)
    {
        $pengiriman = PengirimanBerkas::findOrFail($id);

        $loker = Loker::with('lemari', 'arsips')
            ->where('status', 'aktif')
            ->get()
            ->first(function ($l) {
                return $l->arsips()->count() < $l->kapasitas;
            });

        if (!$loker) {
            return response()->json([
                'message' => 'Tidak ada loker tersedia'
            ], 500);
        }

        $jumlah = $loker->arsips()->count();

        $slot = floor($jumlah / 10) + 1;

        return response()->json([
            'success' => true,
            'data' => [
                'kode_permohonan' => $pengiriman->kode_permohonan,
                'lemari' => $loker->lemari->kode_lemari,
                'loker'  => $loker->kode_loker,
                'slot'   => $slot
            ]
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
            'status' => 'ditolak',
            'berita_acara_id' => null //  reset supaya bisa masuk berita acara lagi
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

    public function bulkTerima(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        DB::transaction(function () use ($request) {

            $pengirimanList = PengirimanBerkas::whereIn('id', $request->ids)
                ->where('status', 'menunggu')
                ->lockForUpdate()
                ->get();

            foreach ($pengirimanList as $pengiriman) {

                $snapshot = $pengiriman->simkim_snapshot;
                $permohonan = $snapshot['permohonan'] ?? null;

                if (!$permohonan) continue;

                $loker = Loker::lockForUpdate()
                    ->where('status', 'aktif')
                    ->get()
                    ->first(fn($l) => $l->jumlahArsip() < $l->kapasitas);

                if (!$loker) continue;

               $lastArsip = Arsip::where('loker_id', $loker->id)
    ->lockForUpdate()
    ->latest('id')
    ->first();

$nomorUrut = $lastArsip
    ? ((int) substr($lastArsip->nomor_arsip, -4)) + 1
    : 1;

$nomorArsip = sprintf(
    "%s.%s.%04d",
    $loker->lemari->kode_lemari,
    $loker->kode_loker,
    $nomorUrut
);

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

                    'nama_lengkap'         => $permohonan['nama_lengkap'] ?? null,
                    'nomor_paspor'         => $permohonan['nopaspor'] ?? null,
                    'tanggal_permohonan'   => $permohonan['tanggal_permohonan'] ?? null,
                    'status_proses'        => $permohonan['alurterakhir'] ?? null,
                ]);

                // update status
                $loker->syncStatus();
                $loker->lemari->syncStatus();

                $pengiriman->update([
                    'status' => 'diterima',
                    'arsip_id' => $arsip->id,
                    'nomor_arsip' => $nomorArsip,
                ]);
            }
        });

        return back()->with('success', 'Berkas berhasil diterima secara massal');
    }
}
