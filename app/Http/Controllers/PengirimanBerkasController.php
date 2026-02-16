<?php

namespace App\Http\Controllers;

use App\Models\PengirimanBerkas;
use App\Service\SimkimApiService;
use App\Services\SimkimApiService as ServicesSimkimApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PengirimanBerkasController extends Controller
{
    /**
     * Halaman pengiriman berkas (FORM)
     */
    public function index()
    {
        $user = Auth::user();

        // ✅ HANYA PETUGAS PENGIRIM
        abort_unless($user->role === 'user', 403);

        return view('user.pengiriman');
    }

    /**
     * Simpan pengiriman berkas
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->role === 'user', 403);

        $validator = Validator::make($request->all(), [
            'kode_permohonan' => 'required|string|max:50|unique:pengiriman_berkas,kode_permohonan',
            'tanggal_kirim'   => 'required|date',
            'catatan'         => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $pengiriman = PengirimanBerkas::create([
                'kode_permohonan'       => trim($request->kode_permohonan),
                'tanggal_kirim'         => $request->tanggal_kirim,
                'asal_berkas'           => Auth::user()->kantor, // 🔥 OTOMATIS
                'catatan'               => $request->catatan ? trim($request->catatan) : null,
                'petugas_pengirim_id'   => Auth::id(),
                'status'                => 'menunggu',
            ]);

            return redirect()
                ->route('user.pengiriman')
                ->with(
                    'success',
                    'Berkas berhasil dikirim. Kode: ' . $pengiriman->kode_permohonan
                );
        } catch (\Exception $e) {
            Log::error('Pengiriman berkas gagal', [
                'error' => $e->getMessage()
            ]);

            return back()
                ->with('error', 'Gagal mengirim berkas')
                ->withInput();
        }
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
    public function fetchSimkim(Request $request,ServicesSimkimApiService $simkim)
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
}
