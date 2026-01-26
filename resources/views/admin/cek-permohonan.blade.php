@extends('layouts.admin')

@section('title', 'Cek Permohonan')

@section('main-content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-search fa-fw mr-2"></i>Cek Status Permohonan
    </h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-question-circle fa-sm text-white-50"></i> Bantuan
    </a>
</div>

<!-- Session Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle mr-2"></i>
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<!-- Search Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-search fa-fw mr-1"></i>Form Pencarian Permohonan
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('cek-permohonan.search') }}" method="POST" class="needs-validation" novalidate>
            @csrf

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="nomor_permohonan" class="font-weight-bold text-gray-800">
                        Nomor Permohonan
                    </label>
                    <input type="text"
                           name="nomor_permohonan"
                           id="nomor_permohonan"
                           value="{{ old('nomor_permohonan', $filters['nomor_permohonan'] ?? '') }}"
                           class="form-control"
                           placeholder="REQ20240001">
                    <small class="form-text text-muted">Contoh: REQ20240001</small>
                </div>

                <div class="form-group col-md-3">
                    <label for="nomor_paspor" class="font-weight-bold text-gray-800">
                        Nomor Paspor
                    </label>
                    <input type="text"
                           name="nomor_paspor"
                           id="nomor_paspor"
                           value="{{ old('nomor_paspor', $filters['nomor_paspor'] ?? '') }}"
                           class="form-control"
                           placeholder="A1234567">
                    <small class="form-text text-muted">Contoh: A1234567</small>
                </div>

                <div class="form-group col-md-3">
                    <label for="nama_pemohon" class="font-weight-bold text-gray-800">
                        Nama Pemohon
                    </label>
                    <input type="text"
                           name="nama_pemohon"
                           id="nama_pemohon"
                           value="{{ old('nama_pemohon', $filters['nama_pemohon'] ?? '') }}"
                           class="form-control"
                           placeholder="Nama lengkap pemohon">
                </div>

                <div class="form-group col-md-3">
                    <label for="tanggal" class="font-weight-bold text-gray-800">
                        Tanggal Permohonan
                    </label>
                    <input type="text"
                           name="tanggal"
                           id="tanggal"
                           value="{{ old('tanggal', $filters['tanggal'] ?? '') }}"
                           class="form-control"
                           placeholder="DD/MM/YYYY"
                           data-toggle="datepicker">
                    <small class="form-text text-muted">Format: 25/12/2023</small>
                </div>
            </div>

            <div class="form-row mt-2">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle mr-1"></i>
                                Isi minimal satu kolom untuk melakukan pencarian
                            </p>
                        </div>
                        <div>
                            @if(isset($permohonan) && $permohonan->isNotEmpty())
                            <a href="{{ route('cek-permohonan.index') }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-times mr-1"></i> Reset
                            </a>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Cari Permohonan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Results Card -->
@if(isset($permohonan) && $permohonan->isNotEmpty())
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list-alt fa-fw mr-1"></i>Hasil Pencarian
            <span class="badge badge-primary ml-2">{{ $permohonan->count() }} data ditemukan</span>
        </h6>
        <form action="{{ route('cek-permohonan.generate-report') }}" method="POST" class="d-inline">
            @csrf
            @foreach($filters as $key => $value)
                @if($value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-file-export mr-1"></i> Export Data
            </button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>No. Permohonan</th>
                        <th>Nama Pemohon</th>
                        <th>No. Paspor</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Lokasi Arsip</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permohonan as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->nomor_permohonan }}</strong>
                        </td>
                        <td>{{ $item->nama_pemohon }}</td>
                        <td>
                            <span class="badge badge-info">{{ $item->nomor_paspor }}</span>
                        </td>
                        <td>
                            @if($item->tanggal)
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusConfig = [
                                    'proses' => ['color' => 'warning', 'icon' => 'clock', 'text' => 'Proses'],
                                    'disetujui' => ['color' => 'success', 'icon' => 'check', 'text' => 'Disetujui'],
                                    'ditolak' => ['color' => 'danger', 'icon' => 'times', 'text' => 'Ditolak'],
                                ];
                                $config = $statusConfig[$item->status] ?? ['color' => 'secondary', 'icon' => 'question', 'text' => ucfirst($item->status)];
                            @endphp
                            <span class="badge badge-{{ $config['color'] }}">
                                <i class="fas fa-{{ $config['icon'] }} mr-1"></i>{{ $config['text'] }}
                            </span>
                        </td>
                        <td>
                            @if($item->lemari && $item->loker && $item->nomor_arsip)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-archive text-primary mr-2"></i>
                                    <div>
                                        <div class="text-xs font-weight-bold">{{ $item->lemari }}, {{ $item->loker }}</div>
                                        <div class="text-xs text-gray-600">No: {{ $item->nomor_arsip }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">
                                    <i class="fas fa-map-marker-alt-slash mr-1"></i>Belum ditentukan
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#detailModal{{ $item->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="#" class="btn btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Detail Modal -->
                    <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailModalLabel{{ $item->id }}">
                                        <i class="fas fa-file-alt mr-2"></i>Detail Permohonan
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="font-weight-bold text-primary">Informasi Pemohon</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="40%">No. Permohonan</td>
                                                    <td><strong>{{ $item->nomor_permohonan }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Nama Pemohon</td>
                                                    <td>{{ $item->nama_pemohon }}</td>
                                                </tr>
                                                <tr>
                                                    <td>No. Paspor</td>
                                                    <td>{{ $item->nomor_paspor }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tanggal</td>
                                                    <td>
                                                        @if($item->tanggal)
                                                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="font-weight-bold text-primary">Status & Lokasi</h6>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="40%">Status</td>
                                                    <td>
                                                        <span class="badge badge-{{ $config['color'] }}">
                                                            {{ $config['text'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Lemari</td>
                                                    <td>{{ $item->lemari ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Loker</td>
                                                    <td>{{ $item->loker ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>No. Arsip</td>
                                                    <td>{{ $item->nomor_arsip ?? '-' }}</td>
                                                </tr>
                                                @if($item->catatan)
                                                <tr>
                                                    <td>Catatan</td>
                                                    <td class="text-muted"><small>{{ $item->catatan }}</small></td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <h6 class="font-weight-bold text-primary">Lokasi Lengkap</h6>
                                            <div class="alert alert-info">
                                                @if($item->lemari && $item->loker && $item->nomor_arsip)
                                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                                    Arsip tersimpan di: <strong>{{ $item->lemari }}, {{ $item->loker }}, {{ $item->nomor_arsip }}</strong>
                                                @else
                                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                                    Lokasi arsip belum ditentukan
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($permohonan instanceof \Illuminate\Pagination\LengthAwarePaginator && $permohonan->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Menampilkan {{ $permohonan->firstItem() }} - {{ $permohonan->lastItem() }} dari {{ $permohonan->total() }} data
            </div>
            <div>
                {{ $permohonan->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@elseif(request()->hasAny(['nomor_permohonan', 'nomor_paspor', 'nama_pemohon', 'tanggal']))
<!-- No Results Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-exclamation-triangle fa-fw mr-1"></i>Tidak Ditemukan
        </h6>
    </div>
    <div class="card-body">
        <div class="text-center py-4">
            <i class="fas fa-search fa-4x text-gray-300 mb-3"></i>
            <h4 class="text-gray-700 mb-3">Tidak ditemukan data permohonan</h4>
            <p class="text-gray-500 mb-4">
                Tidak ada permohonan yang sesuai dengan kriteria pencarian Anda.<br>
                Coba dengan kata kunci lain atau kurangi filter pencarian.
            </p>
            <a href="{{ route('cek-permohonan.index') }}" class="btn btn-primary">
                <i class="fas fa-redo mr-1"></i> Coba Lagi
            </a>
        </div>
    </div>
</div>
@endif


<!-- Custom Styles -->
<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
        cursor: pointer;
    }

    .badge {
        font-size: 0.85em;
        font-weight: 500;
    }

    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .modal-lg {
        max-width: 800px;
    }

    .table-borderless td {
        border: none !important;
    }

    .card {
        border-radius: 0.5rem;
    }
</style>

<!-- Scripts -->
@section('scripts')
<script>
    // Date picker
    $(document).ready(function() {
        // Initialize datepicker
        $('[data-toggle="datepicker"]').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'id'
        });

        // Auto format tanggal input
        $('#tanggal').on('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2 && value.length <= 4) {
                value = value.substring(0, 2) + '/' + value.substring(2);
            } else if (value.length > 4) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4) + '/' + value.substring(4, 8);
            }
            e.target.value = value;
        });

        // DataTable initialization
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
                },
                "order": [[0, "desc"]]
            });
        }

        // Form validation
        $('.needs-validation').on('submit', function(e) {
            const nomorPermohonan = $('#nomor_permohonan').val();
            const nomorPaspor = $('#nomor_paspor').val();
            const namaPemohon = $('#nama_pemohon').val();
            const tanggal = $('#tanggal').val();

            if (!nomorPermohonan && !nomorPaspor && !namaPemohon && !tanggal) {
                e.preventDefault();
                alert('Mohon isi minimal satu kolom pencarian!');
                return false;
            }

            // Validate date format
            if (tanggal) {
                const dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;
                if (!dateRegex.test(tanggal)) {
                    e.preventDefault();
                    alert('Format tanggal tidak valid! Gunakan format DD/MM/YYYY');
                    return false;
                }
            }
        });
    });

    // Print function
    function printPermohonan(id) {
        window.open('/permohonan/' + id + '/print', '_blank');
    }
</script>
@endsection
@stop
