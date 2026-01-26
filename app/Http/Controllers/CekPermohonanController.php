<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CekPermohonanController extends Controller
{
    /**
     * Menampilkan halaman cek permohonan
     * Akan menampilkan view berbeda berdasarkan role
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $permohonan = collect();
        $filters = $request->only(['nomor_permohonan', 'nomor_paspor', 'nama_pemohon', 'tanggal']);

        // Jika ada parameter pencarian, lakukan pencarian
        if ($request->hasAny(['nomor_permohonan', 'nomor_paspor', 'nama_pemohon', 'tanggal'])) {
            $permohonan = Permohonan::search($filters)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Return view berdasarkan role user
        if ($user->role === 'admin') {
            return view('admin.cek-permohonan', compact('permohonan', 'filters'));
        } else {
            return view('user.pengiriman', compact('pengiriman', 'filters'));
        }
    }

    /**
     * Proses pencarian permohonan
     */
    public function search(Request $request)
    {
        $request->validate([
            'nomor_permohonan' => 'nullable|string|max:50',
            'nomor_paspor' => 'nullable|string|max:20',
            'nama_pemohon' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date_format:d/m/Y',
        ]);

        return redirect()->route('cek-permohonan.index', $request->all());
    }

    /**
     * Generate report (PDF/Excel)
     */
    public function generateReport(Request $request)
    {
        $filters = $request->only(['nomor_permohonan', 'nomor_paspor', 'nama_pemohon', 'tanggal']);

        $permohonan = Permohonan::search($filters)
            ->orderBy('created_at', 'desc')
            ->get();

        return redirect()->route('cek-permohonan.index', $filters)
            ->with('success', 'Fitur generate report akan segera tersedia!');
    }
}
