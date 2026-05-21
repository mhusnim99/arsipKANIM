<?php

namespace App\Http\Controllers;

use App\Models\Lemari;
use App\Models\Loker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManajemenLokasiController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $lemaris = Lemari::with('lokers.arsips')
            ->orderByRaw("CAST(SUBSTRING(kode_lemari, 2) AS UNSIGNED) ASC")
            ->paginate(10);

        return view('admin.manajemen-lemari', compact('lemaris'));
    }

    public function create()
    {
        return view('admin.lemari-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lemari' => 'required|string|max:100',
            'keterangan'  => 'nullable|string|max:500',
            'kapasitas_default_loker' => 'required|integer|min:1',
            'jumlah_loker' => 'required|integer|min:1|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $lemari = Lemari::create([
                'nama_lemari' => trim($request->nama_lemari),
                'jumlah_kolom' => 3,
                'jumlah_baris_per_kolom' => 10,
                'keterangan' => $request->keterangan,
                'status' => 'aktif',
                'kapasitas_default_loker' => $request->kapasitas_default_loker,
                'jumlah_loker' => $request->jumlah_loker,
            ]);
            $lemari->generateLokers();
            $lemari->syncStatus();
        });

        return redirect()
            ->route('admin.manajemen-lemari.index')
            ->with('success', 'Lemari berhasil ditambahkan');
    }
    public function show($id)
    {
        $lemari = Lemari::with(['lokers' => function ($q) {
            $q->orderBy('kolom')->orderBy('baris');
        }])->findOrFail($id);
        $totalArsip = $lemari->arsips()->count();
        $totalKapasitas = $lemari->lokers()->sum('kapasitas');
        $sisaKapasitas = max(0, $totalKapasitas - $totalArsip);

        $persentaseTerisi = $totalKapasitas > 0
            ? round(($totalArsip / $totalKapasitas) * 100, 1)
            : 0;

        return view('admin.lemari-detail', compact(
            'lemari',
            'totalArsip',
            'totalKapasitas',
            'sisaKapasitas',
            'persentaseTerisi'
        ));
    }

    public function edit($id)
    {
        $lemari = Lemari::findOrFail($id);

        return view('admin.lemari-edit', compact('lemari'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lemari' => 'required|string|max:100',
            'status'      => 'required|in:aktif,penuh',
            'keterangan'  => 'nullable|string|max:500',
        ]);

        $lemari = Lemari::findOrFail($id);

        DB::transaction(function () use ($lemari, $request) {
            $lemari->update($request->only('nama_lemari', 'status', 'keterangan'));
            $lemari->syncStatus();
        });

        return redirect()
            ->route('admin.manajemen-lemari.index')
            ->with('success', 'Lemari berhasil diperbarui');
    }

    public function destroy($id)
    {
        $lemari = Lemari::with('arsips')->findOrFail($id);

        if ($lemari->arsips()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus lemari yang masih berisi arsip');
        }

        $lemari->delete(); 

        return back()->with('success', 'Lemari berhasil dihapus');
    }

    public function updateKapasitasLoker(Request $request, $id)
    {
        $request->validate([
            'kapasitas' => 'required|integer|min:1|max:1000',
        ]);

        DB::transaction(function () use ($request, $id) {

            $loker = Loker::findOrFail($id);
            $loker->update(['kapasitas' => $request->kapasitas]);
            $loker->syncStatus();
            $loker->lemari->syncStatus();
        });

        return back()->with('success', 'Kapasitas loker berhasil diperbarui');
    }
}
