@extends('layouts.admin')

@section('title', 'Detail Lemari')

@section('main-content')
<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-gray-800 mb-0">
            <i class="fas fa-archive text-primary mr-2"></i>
            Detail Lemari — {{ $lemari->kode_lemari }}
        </h1>

        <div>
            <a href="{{ route('admin.manajemen-lemari.edit', $lemari->id) }}"
               class="btn btn-sm btn-warning shadow-sm mr-2">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>

            <a href="{{ route('admin.manajemen-lemari.index') }}"
               class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ================= SUMMARY CARDS ================= --}}
    <div class="row mb-4">

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm border-left-primary h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Total Arsip
                    </div>
                    <div class="h5 font-weight-bold mb-0">
                        {{ $totalArsip }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm border-left-info h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Total Kapasitas
                    </div>
                    <div class="h5 font-weight-bold mb-0">
                        {{ $totalKapasitas }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm border-left-success h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Sisa Kapasitas
                    </div>
                    <div class="h5 font-weight-bold mb-0">
                        {{ $sisaKapasitas }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card shadow-sm border-left-warning h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Terisi (%)
                    </div>
                    <div class="h5 font-weight-bold mb-1">
                        {{ $persentaseTerisi }}%
                    </div>

                    <div class="progress progress-sm">
                        <div class="progress-bar bg-warning"
                             style="width: {{ $persentaseTerisi }}%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ================= MAIN CONTENT ================= --}}
    <div class="row">

        {{-- INFO LEMARI --}}
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <strong><i class="fas fa-info-circle mr-2"></i>Informasi Lemari</strong>
                </div>

                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <tr>
                            <th width="40%">Kode</th>
                            <td>{{ $lemari->kode_lemari }}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $lemari->nama_lemari }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge badge-pill
                                    {{ $lemari->status === 'aktif' ? 'badge-success' : 'badge-secondary' }} px-3 py-1">
                                    {{ ucfirst($lemari->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $lemari->created_at->format('d/m/Y H:i') }}</td>
                        </tr>

                        @if ($lemari->keterangan)
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $lemari->keterangan }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- DAFTAR LOKER --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white text-center">
                    <strong><i class="fas fa-boxes mr-2"></i>Daftar Loker</strong>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light text-center">
                            <tr>
                                <th>Loker</th>
                                <th>Kapasitas</th>
                                <th>Sisa</th>
                                <th>Status</th>
                                <th width="22%">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                           @foreach ($lemari->lokers as $loker)

                            @php
                                $arsipDiLoker = $loker->arsips->count();
                                $sisaLoker = max(0, $loker->kapasitas - $arsipDiLoker);
                            @endphp

                                <tr>
                                    <td class="font-weight-bold">
                                        {{ $loker->kode_loker }}
                                    </td>

                                    <td>{{ $loker->kapasitas }}</td>

                                   <td>
                                        <span class="badge badge-success px-3">
                                            {{ $sisaLoker }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge badge-pill
                                            {{ $loker->status === 'tersedia'
                                                ? 'badge-primary'
                                                : ($loker->status === 'penuh'
                                                    ? 'badge-danger'
                                                    : 'badge-secondary') }} px-3 py-1">
                                            {{ ucfirst($loker->status) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">

                                            <a href="{{ route('admin.manajemen-lemari.loker.show', $loker->id) }}"
                                               class="btn btn-sm btn-info shadow-sm mr-2"
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('admin.manajemen-lemari.loker.kapasitas', $loker->id) }}"
                                                  class="d-flex align-items-center">
                                                @csrf
                                                @method('PUT')
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection
