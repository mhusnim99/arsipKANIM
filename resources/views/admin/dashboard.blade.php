@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Dashboard Admin
        </h1>
        <span class="badge px-4 py-2 shadow-sm" style="background:#1E3A8A; color:white;">
            <i class="fas fa-calendar-alt"></i> {{ date('l, d F Y') }}
        </span>
    </div>

    <!-- STATISTIK UTAMA -->
    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow border-0 text-white" style="background:linear-gradient(135deg,#1E3A8A,#3B82F6); border-radius:14px">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small">Total Arsip</div>
                        <div class="h3 font-weight-bold">{{ $totalArsip }}</div>
                    </div>
                    <i class="fas fa-archive fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow border-0 text-white" style="background:linear-gradient(135deg,#38BDF8,#0EA5E9); border-radius:14px">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small">Diterima</div>
                        <div class="h3 font-weight-bold">{{ $diterima }}</div>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow border-0 text-white" style="background:linear-gradient(135deg,#f6d365,#fda085); border-radius:14px">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small">Pending</div>
                        <div class="h3 font-weight-bold">{{ $pending }}</div>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow border-0 text-white" style="background:linear-gradient(135deg,#fca5a5,#f87171); border-radius:14px">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small">Ditolak</div>
                        <div class="h3 font-weight-bold">{{ $ditolak }}</div>
                    </div>
                    <i class="fas fa-times-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- KAPASITAS & STATISTIK -->
    <div class="row">

        <div class="col-lg-8 mb-4">
            <div class="card shadow border-0">
                <div class="card-header text-white font-weight-bold" style="background:linear-gradient(135deg,#1E3A8A,#3B82F6)">
                    <i class="fas fa-database mr-1"></i> Kapasitas Penyimpanan
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h6 class="text-muted">Total Kapasitas</h6>
                            <h3 class="font-weight-bold">{{ $totalKapasitas }}</h3>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Terisi</h6>
                            <h3 class="font-weight-bold text-success">{{ $totalTerisi }}</h3>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Sisa</h6>
                            <h3 class="font-weight-bold text-info">{{ $sisaKapasitas }}</h3>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="progress rounded-pill" style="height:22px">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: {{ $persenPemakaian }}%; background:#3B82F6;" role="progressbar">
                                {{ $persenPemakaian }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header text-white font-weight-bold" style="background:linear-gradient(135deg,#38BDF8,#0EA5E9)">
                    <i class="fas fa-warehouse mr-1"></i> Statistik Penyimpanan
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Lemari</span>
                        <span class="badge px-3" style="background:#38BDF8; color:white;">{{ $totalLemari }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Loker</span>
                        <span class="badge px-3" style="background:#0EA5E9; color:white;">{{ $totalLoker }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- GRAFIK & SLOT -->
    <div class="row">

        <div class="col-lg-8 mb-4">
            <div class="card shadow border-0">
                <div class="card-header text-white font-weight-bold" style="background:linear-gradient(135deg,#1E3A8A,#3B82F6)">
                    <i class="fas fa-chart-line mr-1"></i> Grafik Arsip Bulanan
                </div>
                <div class="card-body">
                    <canvas id="chartArsip" height="110"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header text-white font-weight-bold" style="background:linear-gradient(135deg,#fca5a5,#f87171)">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Loker Penuh
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm table-hover text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>Lemari</th>
                                <th>Loker</th>
                                <th>Isi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($slotPenuh as $s)
                            <tr>
                                <td>{{ $s->lemari }}</td>
                                <td>{{ $s->loker }}</td>
                                <td>
                                    <span class="badge" style="background:#f87171; color:white;">
                                        {{ $s->terisi }}/{{ $s->kapasitas }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-muted">Tidak ada loker penuh</td>
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
const ctx = document.getElementById('chartArsip').getContext('2d');
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
            legend: { display: false }
        }
    }
});
</script>
@endpush
