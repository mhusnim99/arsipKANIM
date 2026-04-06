@extends('layouts.user')

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-history text-primary mr-2"></i>Riwayat Pengiriman Berkas
            </h1>
            <div>
                <span class="badge px-4 py-2 shadow-sm" style="background:#1E3A8A; color:white;">
                    <i class="fas fa-undo"></i> Riwayat Pengiriman
                </span>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Pengiriman
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $riwayat->total() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Diterima
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\PengirimanBerkas::where('petugas_pengirim_id', Auth::id())->where('status', 'diterima')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Menunggu
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\PengirimanBerkas::where('petugas_pengirim_id', Auth::id())->where('status', 'menunggu')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Ditolak
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ \App\Models\PengirimanBerkas::where('petugas_pengirim_id', Auth::id())->where('status', 'ditolak')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
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
                        ->where('status','menunggu')
                        ->whereNull('berita_acara_id')
                        ->exists();

                    $beritaAcaraTerakhir = \App\Models\BeritaAcara::where('petugas_pengirim_id', Auth::id())
                        ->latest()
                        ->first();
                @endphp
                    @if($pengirimanMenunggu > 0)

                    <form action="{{ route('user.berita-acara.generate') }}" method="POST">
                        @csrf
                        <button class="btn btn-success btn-sm">
                            <i class="fas fa-file-signature mr-1"></i>
                            Generate Berita Acara
                        </button>
                    </form>

                    @elseif($beritaAcaraTerakhir)

                    <a href="{{ route('user.berita-acara.pdf',$beritaAcaraTerakhir->id) }}"
                    class="btn btn-primary btn-sm">
                        <i class="fas fa-download mr-1"></i>
                        Download Berita Acara
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if ($riwayat->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr class="bg-light">
                                    <th width="5%">No</th>
                                    <th width="15%">Kode Permohonan</th>
                                    <th width="12%">Tanggal Kirim</th>
                                    <th width="20%">Asal Berkas</th>
                                    <th width="12%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($riwayat as $item)
                                    <tr class="table-row" data-status="{{ $item->status }}"
                                        data-asal="{{ $item->asal_berkas }}"
                                        data-bulan="{{ $item->created_at->format('Y-m') }}">
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong class="text-primary">{{ $item->kode_permohonan }}</strong><br>
                                            {{-- <small class="text-muted">ID: {{ $item->id }}</small> --}}
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
                                            @elseif($item->status == 'diterima')
                                                <span class="badge badge-success p-2">
                                                    <i class="fas fa-check mr-1"></i>Diterima
                                                </span>
                                            @else
                                                <span class="badge badge-danger p-2">
                                                    <i class="fas fa-times mr-1"></i>Ditolak
                                                </span>
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
                            Menampilkan {{ $riwayat->firstItem() ?? 0 }} - {{ $riwayat->lastItem() ?? 0 }} dari
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
                        <p class="text-gray-400 mb-4">Anda belum pernah mengirimkan berkas ke sistem arsip.</p>
                        <a href="{{ route('user.pengiriman') }}" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Berkas Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
    <!-- Script untuk filter dan modal -->
@section('scripts')
    <script>
        $(document).ready(function() {
            // Filter functionality
            function applyFilter() {
                const status = $('#filterStatus').val();
                const asal = $('#filterAsal').val();
                const bulan = $('#filterBulan').val();

                $('.table-row').each(function() {
                    const rowStatus = $(this).data('status');
                    const rowAsal = $(this).data('asal');
                    const rowBulan = $(this).data('bulan');

                    let show = true;

                    if (status && rowStatus !== status) show = false;
                    if (asal && rowAsal !== asal) show = false;
                    if (bulan && rowBulan !== bulan) show = false;

                    $(this).toggle(show);
                });
            }

            $('#filterStatus, #filterAsal, #filterBulan').on('change', applyFilter);

            $('#btnResetFilter').on('click', function() {
                $('#filterStatus, #filterAsal').val('');
                $('#filterBulan').val('{{ date('Y-m') }}');
                applyFilter();
            });
                // Set status dengan badge
                const status = $(this).data('status');
                let statusHtml = '';

                switch (status) {
                    case 'menunggu':
                        statusHtml =
                            '<span class="badge badge-warning p-2"><i class="fas fa-clock mr-1"></i>Menunggu</span>';
                        break;
                    case 'diterima':
                        statusHtml =
                            '<span class="badge badge-success p-2"><i class="fas fa-check mr-1"></i>Diterima</span>';
                        break;
                    case 'ditolak':
                        statusHtml =
                            '<span class="badge badge-danger p-2"><i class="fas fa-times mr-1"></i>Ditolak</span>';
                        break;
                }

                $('#modalStatus').html(statusHtml);
            });

            // Auto refresh setiap 60 detik untuk update status
            setInterval(function() {
                location.reload();
            }, 60000);
        });


        function refreshStatus() {
            $('.table-row').each(function() {
                const row = $(this);
                const pengirimanId = row.find('small.text-muted').text().replace('ID: ', '');

                if (pengirimanId) {
                    $.ajax({
                        url: `/user/pengiriman/check-status/${pengirimanId}`,
                        type: 'GET',
                        success: function(response) {
                            if (response.success) {
                                // Update badge status
                                const statusCell = row.find('td:nth-child(5)');
                                const badgeClass = response.status_badge === 'warning' ?
                                    'badge-warning' :
                                    response.status_badge === 'success' ? 'badge-success' :
                                    'badge-danger';
                                const icon = response.status_badge === 'warning' ? 'fa-clock' :
                                    response.status_badge === 'success' ? 'fa-check' : 'fa-times';

                                statusCell.html(`
                            <span class="badge ${badgeClass} p-2">
                                <i class="fas ${icon} mr-1"></i>${response.status_text}
                            </span>
                        `);
                            }
                        }
                    });
                }
            });
        }

        // Jalankan setiap 10 detik
        setInterval(refreshStatus, 10000);
    </script>
@endsection
@endsection
