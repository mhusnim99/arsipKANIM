<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Illuminate\Http\Request;

class MusnahBerkasController extends Controller
{
    public function index(Request $request)
    {
        $query = Arsip::where('status', 'musnah')
            ->orderByDesc('tanggal_musnah')
            ->orderByDesc('id');

        // FILTER NAMA
        if ($request->filled('nama')) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama . '%');
        }

        // FILTER PASPOR
        if ($request->filled('paspor')) {
            $query->where('nomor_paspor', 'like', '%' . $request->paspor . '%');
        }

        // FILTER KODE
        if ($request->filled('kode')) {
            $query->where('kode_permohonan', 'like', '%' . $request->kode . '%');
        }

        $arsips = $query->paginate(10)->withQueryString();

        return view('admin.musnah-berkas', compact('arsips'));
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        Arsip::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'Data berhasil dihapus');
    }
}
