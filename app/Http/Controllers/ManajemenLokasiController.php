<?php

namespace App\Http\Controllers;

use App\Models\Lemari;
use App\Models\Loker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManajemenLokasiController extends Controller
{
    /* =============================
     | INDEX
     =============================*/
    public function index(Request $request)
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $lemaris = Lemari::orderByRaw(
            "CAST(SUBSTRING(kode_lemari, 2) AS UNSIGNED) ASC"
        )->paginate(10);

        return view('admin.manajemen-lemari', compact('lemaris'));
    }

    /* =============================
     | CREATE
     =============================*/
    public function create()
    {
        return view('admin.lemari-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lemari' => 'required|string|max:100',
            'keterangan'  => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {

            // 🔹 Lemari hanya struktur (BUKAN kapasitas arsip)
            $lemari = Lemari::create([
                // 'kode_lemari' => strtoupper(trim($request->kode_lemari)),
                'nama_lemari' => trim($request->nama_lemari),
                'jumlah_kolom' => 3,
                'jumlah_baris_per_kolom' => 10,
                'keterangan' => $request->keterangan,
                'status' => 'aktif',
            ]);

            // 🔹 Generate seluruh loker
            $lemari->generateLokers();

            // 🔹 Pastikan status awal konsisten
            $lemari->syncStatus();
        });

        return redirect()
            ->route('admin.manajemen-lemari.index')
            ->with('success', 'Lemari berhasil ditambahkan');
    }

    /* =============================
     | SHOW
     =============================*/
    public function show($id)
    {
        $lemari = Lemari::with(['lokers' => function ($q) {
            $q->orderBy('kolom')->orderBy('baris');
        }])->findOrFail($id);

        // ===============================
        // HITUNG BERBASIS ARSIP (BENAR)
        // ===============================
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

    /* =============================
     | EDIT
     =============================*/
    public function edit($id)
    {
        $lemari = Lemari::findOrFail($id);

        return view('admin.lemari-edit', compact('lemari'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lemari' => 'required|string|max:100',
            'status'      => 'required|in:aktif,nonaktif',
            'keterangan'  => 'nullable|string|max:500',
        ]);

        $lemari = Lemari::findOrFail($id);

        DB::transaction(function () use ($lemari, $request) {
            $lemari->update($request->only('nama_lemari', 'status', 'keterangan'));

            // 🔹 Sinkron ulang status berdasarkan kondisi aktual
            $lemari->syncStatus();
        });

        return redirect()
            ->route('admin.manajemen-lemari.index')
            ->with('success', 'Lemari berhasil diperbarui');
    }

    /* =============================
     | DELETE
     =============================*/
    public function destroy($id)
    {
        $lemari = Lemari::with('arsips')->findOrFail($id);

        // ❌ Tidak boleh hapus lemari yang masih berisi arsip
        if ($lemari->arsips()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus lemari yang masih berisi arsip');
        }

        $lemari->delete(); // cascade ke lokers

        return back()->with('success', 'Lemari berhasil dihapus');
    }

    /* =============================
     | UPDATE KAPASITAS LOKER
     =============================*/
    public function updateKapasitasLoker(Request $request, $id)
    {
        $request->validate([
            'kapasitas' => 'required|integer|min:1|max:1000',
        ]);

        DB::transaction(function () use ($request, $id) {

            $loker = Loker::findOrFail($id);
            $loker->update(['kapasitas' => $request->kapasitas]);

            // 🔥 WAJIB: sinkron status
            $loker->syncStatus();
            $loker->lemari->syncStatus();
        });

        return back()->with('success', 'Kapasitas loker berhasil diperbarui');
    }
}
