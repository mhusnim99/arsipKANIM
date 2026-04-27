@extends('layouts.user')

@section('main-content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Riwayat Pengiriman Berkas
        </h1>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">

        <!-- TOTAL -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="h4 font-weight-bold text-gray-800">{{ $total }}</div>
                    <div class="h5 font-weight-bold text-primary">Total Pengiriman</div>
                </div>
            </div>
        </div>

        <!-- DITERIMA -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="h4 font-weight-bold text-gray-800">{{ $diterima }}</div>
                    <div class="h5 font-weight-bold text-success">Diterima</div>
                </div>
            </div>
        </div>

        <!-- MENUNGGU -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="h4 font-weight-bold text-gray-800">{{ $menunggu }}</div>
                    <div class="h5 font-weight-bold text-warning">Menunggu</div>
                </div>
            </div>
        </div>

        <!-- DITOLAK -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="h4 font-weight-bold text-gray-800">{{ $ditolak }}</div>
                    <div class="h5 font-weight-bold text-danger">Ditolak</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Daftar Riwayat Pengiriman
            </h6>

            @php
                $pengirimanMenunggu = \App\Models\PengirimanBerkas::where('petugas_pengirim_id', Auth::id())
                    ->where('status', 'menunggu')
                    ->whereNull('berita_acara_id')
                    ->exists();

                $beritaAcaraTerakhir = \App\Models\BeritaAcara::where('petugas_pengirim_id', Auth::id())
                    ->latest()
                    ->first();
            @endphp

            @if ($pengirimanMenunggu)
                <form action="{{ route('user.berita-acara.generate') }}" method="POST">
                    @csrf
                    <button class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-download mr-1"></i> Download Berita Acara
                    </button>
                </form>
            @elseif ($beritaAcaraTerakhir)
                <a href="{{ route('user.berita-acara.pdf', $beritaAcaraTerakhir->id) }}"
                   class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-download mr-1"></i>
                    Download Berita Acara
                </a>
            @endif
        </div>

        <!-- FILTER -->
       <div class="card shadow-sm border-0 mb-3">

    {{-- HEADER --}}
    <div class="card-header bg-white border-0 pb-0">
        <h6 class="mb-0 font-weight-bold text-primary">
            <i class="fas fa-filter mr-2"></i> Filter Data Arsip
        </h6>
        <small class="text-muted">Gunakan filter untuk mempersempit pencarian data</small>
    </div>

    {{-- BODY --}}
    <div class="card-body pt-3">
        <form method="GET">

            <div class="form-row">

                {{-- ASAL BERKAS --}}
                <div class="form-group col-md-4">
                    <select name="asal_berkas" class="form-control">
                        <option value="">Semua Asal</option>
                        @foreach ($listAsalBerkas as $asal)
                            <option value="{{ $asal }}" 
                                {{ request('asal_berkas') == $asal ? 'selected' : '' }}>
                                {{ $asal }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- BULAN --}}
                <div class="form-group col-md-3">
                    <select name="bulan" class="form-control">
                        <option value="">Semua Bulan</option>
                        @foreach ([
                            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                            9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                        ] as $key => $val)
                            <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                                {{ $val }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TAHUN --}}
                <div class="form-group col-md-3">
                    <select name="tahun" class="form-control">
                        <option value="">Semua Tahun</option>
                        @for ($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- ACTION --}}
                <div class="form-group col-md-2 d-flex align-items-end">
                    <div class="w-100">
                        <button class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>

                        <a href="{{ route('user.pengiriman-riwayat') }}" 
                           class="btn btn-outline-secondary btn-block">
                            Reset
                        </a>
                    </div>
                </div>

            </div>

        </form>
    </div>
</div>

        <div class="card-body">
            @if ($riwayat->count() > 0)

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%">
                        <thead>
                            <tr class="bg-light">
                                <th width="5%">No</th>
                                <th width="15%">Kode Permohonan</th>
                                <th width="12%">Tanggal Kirim</th>
                                <th width="20%">Asal Berkas</th>
                                <th width="12%">Status</th>
                                <th width="15%">Alasan Penolakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($riwayat as $item)
                                <tr class="table-row"
                                    data-status="{{ $item->status }}"
                                    data-asal="{{ $item->asal_berkas }}"
                                    data-bulan="{{ $item->created_at->format('Y-m') }}">

                                    <td class="text-center">
                                        {{ $riwayat->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        <strong class="text-primary">
                                            {{ $item->kode_permohonan }}
                                        </strong>
                                    </td>

                                    <td>
                                        <span class="badge badge-info px-3">
                                            {{ $item->tanggal_kirim->format('d/m/Y') }}
                                        </span>
                                    </td>

                                    <td>
                                        <i class="fas fa-map-marker-alt text-secondary mr-1"></i>
                                        {{ $item->asal_berkas }}
                                    </td>

                                    <td>
                                        @if ($item->status == 'menunggu')
                                            <span class="badge badge-warning p-2">
                                                <i class="fas fa-clock mr-1"></i>Menunggu
                                            </span>
                                        @elseif ($item->status == 'diterima')
                                            <span class="badge badge-success p-2">
                                                <i class="fas fa-check mr-1"></i>Diterima
                                            </span>
                                        @else
                                                <span class="badge badge-danger p-2">
                                                    <i class="fas fa-times mr-1"></i>Ditolak
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->alasan_penolakan)
                                                <span 
                                                    class="d-inline-block text-truncate" 
                                                    style="max-width: 250px;" 
                                                    title="{{ $item->alasan_penolakan }}"
                                                >
                                                    {{ $item->alasan_penolakan }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan {{ $riwayat->firstItem() ?? 0 }} -
                        {{ $riwayat->lastItem() ?? 0 }} dari
                        {{ $riwayat->total() }} entri
                    </div>
                    <div>
                        {{ $riwayat->links() }}
                    </div>
                </div>

            @else

                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-500">Belum Ada Riwayat Pengiriman</h4>
                    <p class="text-gray-400 mb-4">
                        Anda belum pernah mengirimkan berkas ke sistem arsip.
                    </p>
                    <a href="{{ route('user.pengiriman') }}" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Kirim Berkas Pertama
                    </a>
                </div>

            @endif
        </div>
    </div>

</div>
@endsection
