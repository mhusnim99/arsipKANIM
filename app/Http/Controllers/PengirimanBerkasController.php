<?php

namespace App\Http\Controllers;

use App\Models\PengirimanBerkas;
use App\Services\SimkimApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\SimkimSync;

class PengirimanBerkasController extends Controller
{
    /**
     * Halaman pengiriman berkas (FORM)
     */
    public function index()
    {
        $syncData = SimkimSync::where('sudah_dikirim', false)
            ->where('status_proses', 'SELESAI')
            ->latest()
            ->get();

        return view('user.pengiriman', compact('syncData'));
    }

    /**
     * Simpan pengiriman berkas
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_permohonan' => 'required|string',
            'simkim_snapshot' => 'required'
        ]);

        $snapshot = json_decode($request->simkim_snapshot, true);

        if (!$snapshot || ($snapshot['permohonan']['alurterakhir'] ?? '') !== 'SELESAI') {
            return back()->with('error', 'Data tidak valid atau belum selesai.');
        }

        if (PengirimanBerkas::where('kode_permohonan', $snapshot['permohonan']['nopermohonan'])
            ->whereIn('status', ['menunggu', 'diterima'])
            ->exists()
        ) {
            return back()->with('error', 'Data sudah pernah dikirim.');
        }

        $pengiriman = PengirimanBerkas::create([
            'kode_permohonan'     => $snapshot['permohonan']['nopermohonan'],
            'tanggal_kirim'       => now(),
            'asal_berkas'         => Auth::user()->kantor,
            'petugas_pengirim_id' => Auth::id(),
            'status'              => 'menunggu',
            'simkim_snapshot'     => $snapshot,
        ]);

        return redirect()
            ->route('user.pengiriman')
            ->with('success', 'Berkas berhasil dikirim dan menunggu verifikasi admin.');
    }

    /**
     * Riwayat pengiriman per petugas
     */
    public function riwayat()
    {
        abort_unless(Auth::user()->role === 'user', 403);

        $riwayat = PengirimanBerkas::where('petugas_pengirim_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('user.pengiriman-riwayat', compact('riwayat'));
    }

    /**
     * Cek status pengiriman (AJAX)
     */
    public function checkStatus($id)
    {
        abort_unless(Auth::user()->role === 'user', 403);

        $pengiriman = PengirimanBerkas::find($id);

        if (! $pengiriman) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return response()->json([
            'success'       => true,
            'status'        => $pengiriman->status,
            'status_text'   => $pengiriman->status_text,
            'status_badge'  => $pengiriman->status_badge,
        ]);
    }
    public function fetchSimkim(Request $request, SimkimApiService $simkim)
    {
        $request->validate([
            'kode_permohonan' => 'required|string'
        ]);

        $result = $simkim->getPermohonanByKode($request->kode_permohonan);

        if (!($result['success'] ?? false)) {
            return back()->with('error', 'Data tidak ditemukan di SIMKIM');
        }

        return back()->with([
            'simkim' => $result['data']
        ]);
    }

    public function berkasDitolak()
    {
        $pengirimanBerkas = PengirimanBerkas::where('petugas_pengirim_id', Auth::id())
            ->where('status', 'ditolak')
            ->latest()
            ->paginate(10);

        return view('user.berkas-ditolak', compact('pengirimanBerkas'));
    }

    public function kirimPerbaikan($id)
    {
        $data = PengirimanBerkas::where('petugas_pengirim_id', Auth::id())
            ->where('status', 'ditolak')
            ->findOrFail($id);

        $data->update([
            'status' => 'menunggu',
            'alasan_penolakan' => null,
            'ditolak_pada' => null
        ]);

        return redirect()->route('user.pengiriman.ditolak')
            ->with('success', 'Berkas berhasil dikirim ulang ke admin');
    }
    public function kirimDariSync($id)
    {
        $sync = \App\Models\SimkimSync::findOrFail($id);

        if ($sync->sudah_dikirim) {
            return back()->with('error', 'Data sudah dikirim.');
        }

        DB::transaction(function () use ($sync) {

            PengirimanBerkas::create([
                'kode_permohonan'     => $sync->kode_permohonan,
                'tanggal_kirim'       => now(),
                'asal_berkas'         => Auth::user()->kantor,
                'petugas_pengirim_id' => Auth::id(),
                'status'              => 'menunggu',
                'simkim_snapshot'     => $sync->data_snapshot,
            ]);

            $sync->update([
                'sudah_dikirim' => true
            ]);
        });

        return back()->with('success', 'Berkas berhasil dikirim dari data sinkronisasi.');
    }
}
