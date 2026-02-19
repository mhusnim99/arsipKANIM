<?php

namespace App\Http\Controllers;

use App\Models\PengirimanBerkas;
use App\Models\Arsip;
use App\Models\Lemari;
use App\Models\Loker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $q->where('status', 'tersedia')
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

            // LOCK pengiriman
            $pengiriman = PengirimanBerkas::lockForUpdate()->findOrFail($id);

            if ($pengiriman->status !== 'menunggu') {
                throw new \Exception('Berkas sudah diproses');
            }

            // Cari loker pertama yang masih tersedia
            $loker = Loker::lockForUpdate()
                ->where('status', 'tersedia')
                ->whereColumn(
                    'kapasitas',
                    '>',
                    DB::raw('(select count(*) from arsips where arsips.loker_id = lokers.id)')
                )
                ->orderBy('lemari_id')
                ->orderBy('kolom')
                ->orderBy('baris')
                ->firstOrFail();

            // Hitung jumlah arsip di loker
            $totalArsip = Arsip::where('loker_id', $loker->id)->count();

            // Hitung slot (10 arsip = 1 slot)
            $slot = ceil(($totalArsip + 1) / 10);

            if ($slot > 35) {
                throw new \Exception('Slot loker sudah penuh');
            }

            $slotFormatted = str_pad($slot, 2, '0', STR_PAD_LEFT);

            // Simpan arsip
            $arsip = Arsip::create([
                'pengiriman_berkas_id' => $pengiriman->id,
                'kode_permohonan'      => $pengiriman->kode_permohonan,
                'nomor_arsip'          => $slotFormatted,
                'tanggal_masuk'        => now(),
                'asal_berkas'          => $pengiriman->asal_berkas,
                'lemari_id'            => $loker->lemari_id,
                'loker_id'             => $loker->id,
                'status'               => 'tersimpan',
                'diterima_oleh'        => Auth::id(),
            ]);

            // Update loker
            $loker->increment('terisi');
            $loker->refresh();
            $loker->syncStatus();

            // Update lemari
            $loker->lemari->refresh();
            $loker->lemari->syncStatus();

            // Update pengiriman
            $pengiriman->update([
                'status'      => 'diterima',
                'arsip_id'    => $arsip->id,
                'nomor_arsip' => $slotFormatted,
            ]);

            return [
                'kode_permohonan' => $pengiriman->kode_permohonan,
                'lokasi' => 'Lemari ' . $loker->lemari->kode_lemari .
                            ' / Loker ' . $loker->kode_loker .
                            ' / Slot ' . $slotFormatted,
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
    $request->validate([
        'alasan_penolakan' => 'required|string',
    ]);

    PengirimanBerkas::where('id', $id)->update([
        'status' => 'ditolak',
        'alasan_penolakan' => $request->alasan_penolakan,
        'ditolak_pada' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Berkas berhasil ditolak',
    ]);
}

    /**
     * Ambil loker berdasarkan lemari (AJAX)
     */
    public function getLokersByLemari(Lemari $lemari)
    {
        $lokers = $lemari->lokers()
            ->where('status', 'tersedia')
            ->whereColumn(
                'kapasitas',
                '>',
                DB::raw('(select count(*) from arsips where arsips.loker_id = lokers.id)')
            )
            ->orderBy('kolom')
            ->orderBy('baris')
            ->get();

        return response()->json(
            $lokers->map(fn ($loker) => [
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
        $pengiriman = PengirimanBerkas::with('petugasPengirim')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id'              => $pengiriman->id,
                'kode_permohonan' => $pengiriman->kode_permohonan,
                'asal_berkas'     => $pengiriman->asal_berkas,
                'tanggal_kirim'   => $pengiriman->tanggal_kirim,
                'catatan'         => $pengiriman->catatan,
                'created_at'      => $pengiriman->created_at,
                'petugas_pengirim'=> $pengiriman->petugasPengirim,
                'status'          => $pengiriman->status,
            ],
        ]);
    }
}
