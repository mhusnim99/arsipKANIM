<?php

namespace App\Http\Controllers;

use App\Models\PengirimanBerkas;
use App\Services\SimkimApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SimkimSync;

class PengirimanBerkasController extends Controller
{
    /* ================= HELPER ================= */

    private function isKodeAllowed($kode)
    {
        $user = Auth::user();

        if ($user->email === 'kanim@arsip.com') {
            return str_starts_with($kode, '107');
        }
        if ($user->email === 'ciwo@arsip.com') {
            return str_starts_with($kode, '292');
        }
        if ($user->email === 'wiyung@arsip.com') {
            return str_starts_with($kode, '221');
        }
        if ($user->email === 'bgj@arsip.com') {
            return str_starts_with($kode, '280');
        }
        if ($user->email === 'mjk@arsip.com') {
            return str_starts_with($kode, '271');
        }
        if ($user->email === 'bendul@arsip.com') {
            return str_starts_with($kode, '107');
        }
        if ($user->email === 'mpp@arsip.com') {
            return str_starts_with($kode, '107');
        }

        return true;
    }

    /* ================= INDEX ================= */

    public function index()
    {
        $syncData = SimkimSync::where('sudah_dikirim', false)
            ->where('status_proses', 'SELESAI')
            ->latest()
            ->get();

        return view('user.pengiriman', compact('syncData'));
    }

    /* ================= STORE SINGLE ================= */

    public function store(Request $request)
    {
        $request->validate([
            'kode_permohonan' => 'required|string',
            'simkim_snapshot' => 'required'
        ]);

        $snapshot = json_decode($request->simkim_snapshot, true);
        $permohonan = $snapshot['permohonan'] ?? null;

        if (!$snapshot || !$permohonan) {
            return back()->with('error', 'Data tidak valid.');
        }

        $kode = $permohonan['nopermohonan'] ?? '';
        if (!$this->isKodeAllowed($kode)) {
            return back()->with('error', 'Anda hanya boleh mengirim kode dengan awalan 107.');
        }

        if (($permohonan['alurterakhir'] ?? '') !== 'SELESAI') {
            return back()->with('error', 'Data belum selesai.');
        }

        if (PengirimanBerkas::where('kode_permohonan', $kode)
            ->whereIn('status', ['menunggu', 'diterima'])
            ->exists()
        ) {
            return back()->with('error', 'Data sudah pernah dikirim.');
        }

        PengirimanBerkas::create([
            'kode_permohonan'     => $kode,
            'tanggal_kirim'       => now(),
            'asal_berkas'         => $snapshot['upt']['nama'] ?? 'Tidak diketahui',
            'petugas_pengirim_id' => Auth::id(),
            'status'              => 'menunggu',
            'simkim_snapshot'     => $snapshot,
        ]);

        return redirect()->route('user.pengiriman')
            ->with('success', 'Berkas berhasil dikirim.');
    }

    /* ================= FETCH SIMKIM ================= */

    public function fetchSimkim(Request $request, SimkimApiService $simkim)
    {
        $request->validate([
            'kode_permohonan' => 'required|string'
        ]);

        $kodeList = preg_split('/[\s,]+/', trim($request->kode_permohonan));

        $results = [];
        $errors = [];
        $duplicates = [];

        foreach ($kodeList as $kode) {

            if (empty($kode)) continue;
            if (!$this->isKodeAllowed($kode)) {
                $errors[] = $kode;
                continue;
            }

            // CEK DUPLIKAT
            $exists = PengirimanBerkas::where('kode_permohonan', $kode)
                ->whereIn('status', ['menunggu', 'diterima'])
                ->exists();

            if ($exists) {
                $duplicates[] = $kode;
                continue;
            }

            try {
                $result = $simkim->getPermohonanByKode($kode);

                if (!empty($result) && isset($result['data'])) {
                    $results[] = $result['data'];
                } else {
                    $errors[] = $kode;
                }
            } catch (\Exception $e) {
                $errors[] = $kode;
            }

            usleep(200000);
        }

        if (count($results) === 0) {
            return back()->with('error', 'Data sudah pernah dikirim atau tidak ditemukan. Periksa kembali data yang dimasukkan!');
        }

        return back()->with([
            'simkim_multiple' => $results,
            'simkim_error' => $errors,
            'simkim_duplicate' => $duplicates
        ]);
    }

    /* ================= STORE MULTIPLE ================= */

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'data' => 'required|array'
        ]);

        foreach ($request->data as $json) {

            $snapshot = json_decode($json, true);
            $permohonan = $snapshot['permohonan'] ?? null;

            if (!$permohonan) continue;

            $kode = $permohonan['nopermohonan'] ?? '';

            if (!$this->isKodeAllowed($kode)) {
                return back()->with(
                    'error',
                    "Kode {$kode} tidak diizinkan. Hanya boleh awalan 107."
                );
            }

            // VALIDASI STATUS
            if (strtoupper($permohonan['alurterakhir'] ?? '') !== 'SELESAI') {
                return back()->with(
                    'error',
                    "Kode {$kode} belum SELESAI"
                );
            }

            // CEK DUPLIKAT
            if (PengirimanBerkas::where('kode_permohonan', $kode)
                ->whereIn('status', ['menunggu', 'diterima'])
                ->exists()
            ) {
                continue;
            }

            PengirimanBerkas::create([
                'kode_permohonan'     => $kode,
                'tanggal_kirim'       => now(),
                'asal_berkas'         => $snapshot['upt']['nama'] ?? 'Tidak diketahui',
                'petugas_pengirim_id' => Auth::id(),
                'status'              => 'menunggu',
                'simkim_snapshot'     => $snapshot,
            ]);
        }

        return redirect()->route('user.pengiriman')
            ->with('success', 'Semua berkas berhasil dikirim.');
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

    /* ================= RIWAYAT ================= */

    public function riwayat(Request $request)
    {
        abort_unless(Auth::user()->role === 'user', 403);

        $query = PengirimanBerkas::where('petugas_pengirim_id', Auth::id());
        if ($request->filled('asal_berkas')) {
            $query->where('asal_berkas', $request->asal_berkas);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_kirim', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_kirim', $request->tahun);
        }

        $total = (clone $query)->count();
        $diterima = (clone $query)->where('status', 'diterima')->count();
        $menunggu = (clone $query)->where('status', 'menunggu')->count();
        $ditolak = (clone $query)->where('status', 'ditolak')->count();

        $riwayat = $query->latest()->paginate(10)->withQueryString();
        $listAsalBerkas = PengirimanBerkas::where('petugas_pengirim_id', Auth::id())
            ->select('asal_berkas')
            ->distinct()
            ->orderBy('asal_berkas')
            ->pluck('asal_berkas');

        return view('user.pengiriman-riwayat', compact(
            'riwayat',
            'total',
            'diterima',
            'menunggu',
            'ditolak',
            'listAsalBerkas'
        ));
    }
}
