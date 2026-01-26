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
        'loker:id,lemari_id,kolom,baris'
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
    public function pinjam(Request $request, Arsip $arsip) {}
    public function kembali(Request $request, Arsip $arsip) {}
    public function musnah(Request $request, Arsip $arsip) {}
}
