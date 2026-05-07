@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Dashboard Admin
        </h1>

        <span class="badge px-4 py-2 shadow-sm"
              style="background:#1E3A8A; color:white;">
            <i class="fas fa-calendar-alt mr-1"></i>
            {{ date('l, d F Y') }}
        </span>
    </div>

    {{-- STATISTIK --}}
    <div class="row">

        {{-- TOTAL ARSIP --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow border-0 text-white"
                 style="background:linear-gradient(135deg,#1E3A8A,#3B82F6); border-radius:14px">

                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Total Arsip</div>
                        <div class="h3 font-weight-bold mb-0">
                            {{ $totalArsip }}
                        </div>
                    </div>

                    <i class="fas fa-archive fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- DITERIMA --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow border-0 text-white"
                 style="background:linear-gradient(135deg,#38BDF8,#0EA5E9); border-radius:14px">

                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Diterima</div>
                        <div class="h3 font-weight-bold mb-0">
                            {{ $diterima }}
                        </div>
                    </div>

                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- MENUNGGU --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow border-0 text-white"
                 style="background:linear-gradient(135deg,#f6d365,#fda085); border-radius:14px">

                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Menunggu</div>
                        <div class="h3 font-weight-bold mb-0">
                            {{ $menunggu }}
                        </div>
                    </div>

                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- DITOLAK --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow border-0 text-white"
                 style="background:linear-gradient(135deg,#fca5a5,#f87171); border-radius:14px">

                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small">Ditolak</div>
                        <div class="h3 font-weight-bold mb-0">
                            {{ $ditolak }}
                        </div>
                    </div>

                    <i class="fas fa-times-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- KAPASITAS --}}
    <div class="row">

        {{-- KAPASITAS PENYIMPANAN --}}
        <div class="col-lg-8 mb-4">

            <div class="card shadow border-0">

                <div class="card-header text-white font-weight-bold"
                     style="background:linear-gradient(135deg,#1E3A8A,#3B82F6)">
                    <i class="fas fa-database mr-1"></i>
                    Kapasitas Penyimpanan
                </div>

                <div class="card-body">

                    <div class="row text-center">

                        <div class="col-md-4">
                            <h6 class="text-muted">Total Kapasitas</h6>
                            <h3 class="font-weight-bold">
                                {{ $totalKapasitas }}
                            </h3>
                        </div>

                        <div class="col-md-4">
                            <h6 class="text-muted">Terisi</h6>
                            <h3 class="font-weight-bold text-success">
                                {{ $totalTerisi }}
                            </h3>
                        </div>

                        <div class="col-md-4">
                            <h6 class="text-muted">Sisa</h6>
                            <h3 class="font-weight-bold text-info">
                                {{ $sisaKapasitas }}
                            </h3>
                        </div>

                    </div>

                    <div class="mt-4">
                        <div class="progress rounded-pill" style="height:22px">

                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                 role="progressbar"
                                 style="width: {{ $persenPemakaian }}%; background:#3B82F6;">

                                {{ $persenPemakaian }}%

                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- STATISTIK PENYIMPANAN --}}
        <div class="col-lg-4 mb-4">

            <div class="card shadow border-0">

                <div class="card-header text-white font-weight-bold"
                     style="background:linear-gradient(135deg,#38BDF8,#0EA5E9)">
                    <i class="fas fa-warehouse mr-1"></i>
                    Statistik Penyimpanan
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">
                        <span>Lemari</span>

                        <span class="badge px-3 py-2"
                              style="background:#38BDF8; color:white;">
                            {{ $totalLemari }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Loker</span>

                        <span class="badge px-3 py-2"
                              style="background:#0EA5E9; color:white;">
                            {{ $totalLoker }}
                        </span>
                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- AKTIVITAS --}}
    <div class="row">

        {{-- AKTIVITAS TERBARU --}}
        <div class="col-lg-8 mb-4">

            <div class="card shadow border-0">

                <div class="card-header text-white font-weight-bold"
                     style="background:linear-gradient(135deg,#1E3A8A,#3B82F6)">
                    <i class="fas fa-history mr-1"></i>
                    Aktivitas Arsip Terbaru
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead class="thead-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nomor Arsip</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($arsipTerbaru as $arsip)

                                <tr>

                                    <td class="font-weight-bold">
                                        {{ $arsip->kode_permohonan }}
                                    </td>

                                    <td>
                                        {{ $arsip->nomor_arsip }}
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($arsip->tanggal_masuk)->format('d M Y') }}
                                    </td>

                                    <td>

                                        @if($arsip->status == 'tersimpan')
                                            <span class="badge badge-success px-3 py-2">
                                                Tersimpan
                                            </span>
                                        @elseif($arsip->status == 'dipinjam')
                                            <span class="badge badge-warning px-3 py-2">
                                                Dipinjam
                                            </span>
                                        @else
                                            <span class="badge badge-danger px-3 py-2">
                                                Musnah
                                            </span>
                                        @endif

                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="4"
                                        class="text-center text-muted py-4">
                                        Tidak ada aktivitas arsip
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        {{-- ARSIP TERLAMBAT --}}
        <div class="col-lg-4 mb-4">

            <div class="card shadow border-0">

                <div class="card-header text-white font-weight-bold"
                     style="background:linear-gradient(135deg,#f97316,#ea580c)">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Arsip Terlambat
                </div>

                <div class="card-body p-2">

                    <table class="table table-sm table-hover mb-0">

                        <thead class="thead-light">
                            <tr>
                                <th>Kode</th>
                                <th>Peminjam</th>
                                <th>Hari</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($arsipTerlambat as $item)

                           @php
                                $hari = (int) \Carbon\Carbon::parse($item->tanggal_pinjam)
                                    ->diffInDays(now());
                            @endphp

                            <tr>

                                <td class="font-weight-bold">
                                    {{ $item->kode_permohonan }}
                                </td>

                                <td>
                                    {{ $item->dipinjam_oleh }}
                                </td>

                                <td>

                                    <span class="badge badge-danger px-3 py-2">
                                        {{ $hari }} Hari
                                    </span>

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="3"
                                    class="text-center text-muted py-4">
                                    Tidak ada keterlambatan
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('chartArsip');

if (ctx) {

    new Chart(ctx, {

        type: 'line',

        data: {
            labels: {!! json_encode($bulan) !!},

            datasets: [{
                label: 'Jumlah Arsip',
                data: {!! json_encode($jumlah) !!},

                borderWidth: 4,
                borderColor: '#1E3A8A',
                backgroundColor: 'rgba(30,58,138,0.15)',

                fill: true,
                tension: 0.4,

                pointRadius: 5,
                pointBackgroundColor: '#1E3A8A'
            }]
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    display: false
                }
            }
        }

    });

}

</script>

@endpush
