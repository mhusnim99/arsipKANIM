@extends('layouts.admin')

@section('title', 'Detail Loker')

@section('main-content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-gray-800 mb-0">
            <i class="fas fa-box-open text-primary mr-2"></i>
            Detail Loker — {{ $loker->kode_loker }}
        </h1>

        <a href="{{ route('admin.manajemen-lemari.show', $loker->lemari_id) }}"
           class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    {{-- INFO CARDS --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase">
                        Lemari
                    </div>
                    <div class="h6 font-weight-bold">
                        {{ $loker->lemari->nama_lemari }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-left-info">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase">
                        Kapasitas
                    </div>
                    <div class="h6 font-weight-bold">
                        {{ $loker->kapasitas }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase">
                        Terisi
                    </div>
                    <div class="h6 font-weight-bold">
                        {{ $loker->arsips->count() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-left-warning">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase">
                        Status
                    </div>
                    <div class="h6 font-weight-bold">
                        <span class="badge badge-{{ $loker->status_badge }} px-3 py-1">
                            {{ $loker->status_text }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- DETAIL INFO --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <strong><i class="fas fa-info-circle mr-2"></i>Informasi Loker</strong>
        </div>

        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <tr>
                    <th width="30%">Kode Loker</th>
                    <td>{{ $loker->kode_loker }}</td>
                </tr>
                <tr>
                    <th>Lemari</th>
                    <td>{{ $loker->lemari->nama_lemari }}</td>
                </tr>
                <tr>
                    <th>Kapasitas</th>
                    <td>{{ $loker->kapasitas }}</td>
                </tr>
                <tr>
                    <th>Jumlah Arsip</th>
                    <td>{{ $loker->arsips->count() }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge badge-{{ $loker->status_badge }} px-3 py-1">
                            {{ $loker->status_text }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- DAFTAR ARSIP --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white text-center">
            <strong><i class="fas fa-folder-open mr-2"></i>Daftar Arsip</strong>
        </div>

        <div class="card-body table-responsive">

            @if ($loker->arsips->isEmpty())
                <div class="alert alert-info text-center mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    Loker ini belum memiliki arsip.
                </div>
            @else

                <table class="table table-bordered table-hover">
                    <thead class="bg-light text-center">
                        <tr>
                            <th width="50">No</th>
                            <th>Slot</th>
                            <th>Kode Permohonan</th>
                            <th>Asal Berkas</th>
                            <th>Tanggal Masuk</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody class="text-center">
                        @foreach ($loker->arsips as $i => $arsip)
                            <tr>
                                <td>{{ $i + 1 }}</td>

                                @php
                                    $slot = ceil(($i + 1) / 10);
                                    $start = ($slot - 1) * 10 + 1;
                                    $end = $slot * 10;
                                @endphp
                                <td>
                                    <span class="badge badge-primary px-3 py-1">
                                        Slot {{ $slot }} ({{ $start }}-{{ $end }})
                                    </span>
                                </td>

                                <td>{{ $arsip->kode_permohonan }}</td>
                                <td>{{ $arsip->asal_berkas }}</td>
                                <td>{{ \Carbon\Carbon::parse($arsip->tanggal_masuk)->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = match ($arsip->status) {
                                            'tersimpan' => 'success',
                                            'dipinjam'  => 'warning',
                                            'musnah'    => 'danger',
                                            default     => 'secondary',
                                        };
                                    @endphp

                                    <span class="badge badge-{{ $badgeClass }} px-3 py-1">
                                        {{ ucfirst($arsip->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @endif

        </div>
    </div>

</div>
@endsection
