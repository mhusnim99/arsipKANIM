<?php

namespace App\Http\Controllers;

use App\Models\PengirimanBerkas;
use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengirimanBerkasController extends Controller
{
    public function index()
    {
        // USER hanya melihat form pengiriman
        return view('user.pengiriman');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_permohonan' => 'required|unique:pengiriman_berkas,kode_permohonan',
            'tanggal_kirim'  => 'required|date',
            'asal_berkas'    => 'required',
            'catatan'        => 'nullable|string'
        ]);

        PengirimanBerkas::create([
            'kode_permohonan'      => $request->kode_permohonan,
            'tanggal_kirim'       => $request->tanggal_kirim,
            'asal_berkas'         => $request->asal_berkas,
            'catatan'             => $request->catatan,
            'petugas_pengirim_id'=> Auth::id(),
            'status'              => 'menunggu'
        ]);

        return redirect()->back()->with('success', 'Berkas berhasil dikirim');
    }

    public function riwayat()
    {
        $pengirimanBerkas = PengirimanBerkas::where('petugas_pengirim_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.pengiriman-riwayat', compact('pengirimanBerkas'));
    }

    public function checkStatus($id)
    {
        $data = PengirimanBerkas::where('petugas_pengirim_id', Auth::id())
            ->findOrFail($id);

        return response()->json($data);
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

}
