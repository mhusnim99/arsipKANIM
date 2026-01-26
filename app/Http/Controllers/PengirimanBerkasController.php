<?php
// app/Http/Controllers/PengirimanBerkasController.php

namespace App\Http\Controllers;

use App\Models\PengirimanBerkas;
use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PengirimanBerkasController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Role user adalah 'user' (petugas layanan)
        if (!isset($user->role) || $user->role !== 'user') {
            abort(403, 'Akses hanya untuk Petugas Layanan');
        }

        return view('user.pengiriman');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'kode_permohonan' => 'required|string|max:50|unique:pengiriman_berkas',
            'tanggal_kirim' => 'required|date',
            'asal_berkas' => 'required|string|max:255', // Sesuaikan dengan database (255)
            'catatan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        try {
            // Debug: lihat data sebelum disimpan
            // Log::info('Data pengiriman:', $validated);

            // Simpan data dengan trim untuk menghilangkan spasi ekstra
            $pengiriman = PengirimanBerkas::create([
                'kode_permohonan' => trim($validated['kode_permohonan']),
                'tanggal_kirim' => $validated['tanggal_kirim'],
                'asal_berkas' => trim($validated['asal_berkas']), // PERBAIKAN DI SINI
                'catatan' => isset($validated['catatan']) ? trim($validated['catatan']) : null,
                'petugas_pengirim_id' => Auth::id(),
                'status' => 'menunggu',
            ]);

            return redirect()
                ->route('user.pengiriman')
                ->with('success', 'Berkas berhasil dikirim! Kode: ' . $pengiriman->kode_permohonan);
        } catch (\Exception $e) {
            Log::error('Error menyimpan pengiriman berkas: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Gagal mengirim berkas. Error: ' . $e->getMessage())
                ->withInput();
        }
        DB::transaction(function () use ($pengiriman) {
            Arsip::create([
                'pengiriman_berkas_id' => $pengiriman->id,
                'kode_permohonan' => $pengiriman->kode_permohonan,
                'status' => 'menunggu'
            ]);
        });
    }

    public function riwayat()
    {
        $user = Auth::user();

        if (!isset($user->role) || $user->role !== 'user') {
            abort(403, 'Akses hanya untuk Petugas Layanan');
        }

        $riwayat = PengirimanBerkas::where('petugas_pengirim_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.pengiriman-riwayat', compact('riwayat'));
    }
    public function checkStatus($id)
    {
        $pengiriman = PengirimanBerkas::find($id);

        if (!$pengiriman) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        return response()->json([
            'success' => true,
            'status' => $pengiriman->status,
            'status_text' => $pengiriman->getStatusTextAttribute(),
            'status_badge' => $pengiriman->getStatusBadgeAttribute()
        ]);
    }
}
