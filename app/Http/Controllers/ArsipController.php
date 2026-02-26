<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArsipController extends Controller
{
    public function index(Request $request)
{
    $query = Arsip::with([
    'lemari:id,kode_lemari,nama_lemari',
    'loker:id,lemari_id,kode_loker,kolom,baris'
])
->orderBy('tanggal_masuk', 'desc');

    /* =====================
     | SEARCH (UTAMA)
     ===================== */
    if ($request->filled('q')) {
        $keyword = trim($request->q);

        $query->where(function ($q) use ($keyword) {
            $q->where('kode_permohonan', 'like', "%{$keyword}%")
              ->orWhere('nomor_arsip', 'like', "%{$keyword}%");
        });
    }

    /* =====================
     | FILTER STATUS
     ===================== */
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    /* =====================
     | PAGINATION
     ===================== */
    $arsips = $query->paginate(15)->withQueryString();

    return view('admin.arsip-index', compact('arsips'));
}
    public function show(Arsip $arsip)
{
    $arsip->load([
        'lemari',
        'loker',
        'petugasPenerima',
        'pengiriman'
    ]);

    return view('admin.arsip-show', compact('arsip'));
}

public function update(Request $request, Arsip $arsip)
{
    if ($request->status == 'dipinjam') {
        $request->validate([
            'dipinjam_oleh'   => 'required',
            'keperluan'       => 'required',
            'tanggal_pinjam' => 'required|date',
        ]);

        $arsip->update([
            'status'          => 'dipinjam',
            'dipinjam_oleh'  => $request->dipinjam_oleh,
            'keperluan'      => $request->keperluan,
            'tanggal_pinjam'=> $request->tanggal_pinjam,
        ]);
    }

    elseif ($request->status == 'musnah') {
        $request->validate([
            'dimusnahkan_oleh' => 'required',
            'tanggal_musnah'  => 'required|date',
        ]);

        $arsip->update([
            'status'            => 'musnah',
            'dimusnahkan_oleh' => $request->dimusnahkan_oleh,
            'tanggal_musnah'   => $request->tanggal_musnah,
        ]);
    }

    else {
        $arsip->update(['status' => 'tersimpan']);
    }

    return back()->with('success', 'Status arsip berhasil diperbarui');
}
}
