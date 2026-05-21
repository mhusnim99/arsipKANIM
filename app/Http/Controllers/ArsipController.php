<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HistoryPeminjamanArsip;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = Arsip::with([
            'lemari:id,kode_lemari,nama_lemari',
            'loker:id,lemari_id,kode_loker,kolom,baris'
        ])

            //filter 3 hari
            ->orderByRaw("
    CASE
        WHEN status = 'dipinjam'
        AND tanggal_pinjam IS NOT NULL
        AND tanggal_pinjam <= NOW() - INTERVAL 3 DAY
        THEN 0
        ELSE 1
    END
")
            ->orderByDesc('tanggal_masuk')

            //testing
            // ->orderByRaw("
            //     CASE
            //         WHEN status = 'dipinjam' THEN 0
            //         ELSE 1
            //     END
            // ")
            // ->orderByDesc('tanggal_masuk')

            ->orderByDesc('id');

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
            'pengiriman',
            'histories'
        ]);

        return view('admin.arsip-show', compact('arsip'));
    }


    public function update(Request $request, Arsip $arsip)
    {
        $user = Auth::user();

        if ($arsip->status === 'musnah' && $user->role !== 'admin') {
            return back()->with(
                'error',
                'Arsip yang sudah dimusnahkan tidak dapat diubah oleh petugas.'
            );
        }

        DB::transaction(function () use ($request, $arsip) {

            /*pinjam*/
            if ($request->status === 'dipinjam') {

                $request->validate([
                    'dipinjam_oleh'  => 'required',
                    'keperluan'      => 'required',
                    'tanggal_pinjam' => 'required|date',
                ]);

                $arsip->update([
                    'status'         => 'dipinjam',
                    'dipinjam_oleh'  => $request->dipinjam_oleh,
                    'keperluan'      => $request->keperluan,
                    'tanggal_pinjam' => $request->tanggal_pinjam,
                ]);

                // simpan history
                HistoryPeminjamanArsip::create([
                    'arsip_id'        => $arsip->id,
                    'kode_permohonan' => $arsip->kode_permohonan,
                    'peminjam'        => $request->dipinjam_oleh,
                    'keperluan'       => $request->keperluan,
                    'tanggal_pinjam'  => $request->tanggal_pinjam,
                    'diproses_oleh'   => Auth::id(),
                ]);
            }

            /*dikembalikan*/ else {

                $arsip->update([
                    'status' => 'tersimpan',
                    'dipinjam_oleh' => null,
                    'keperluan' => null,
                    'tanggal_pinjam' => null,
                ]);

                // update history terakhir
                HistoryPeminjamanArsip::where('arsip_id', $arsip->id)
                    ->whereNull('tanggal_kembali')
                    ->latest()
                    ->first()
                    ?->update([
                        'tanggal_kembali' => now()
                    ]);
            }
        });

        return back()->with(
            'success',
            'Status arsip berhasil diperbarui'
        );
    }
}
