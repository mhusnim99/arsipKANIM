@extends('layouts.admin')

@section('main-content')
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                Detail Lemari – {{ $lemari->kode_lemari }}
            </h1>
            <div>
                <a href="{{ route('admin.manajemen-lemari.edit', $lemari->id) }}" class="btn btn-warning mr-2">
                    <i class="fas fa-edit mr-1"></i>Edit Lemari
                </a>
                <a href="{{ route('admin.manajemen-lemari.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali
                </a>
            </div>
        </div>

        @php
            $totalLoker = $lemari->lokers->count();
            $totalKapasitas = $lemari->lokers->sum('kapasitas');
            $lokerKosong = $lemari->lokers->where('status', 'nonaktif')->count();
            $lokerTerisi = $lemari->lokers->whereIn('status', ['aktif', 'penuh'])->count();
        @endphp

        <!-- Ringkasan -->
        <div class="row mb-4">

            {{-- TOTAL ARSIP --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-left-primary">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">
                            Total Arsip
                        </div>
                        <div class="h5 font-weight-bold">
                            {{ $totalArsip }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOTAL KAPASITAS --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-left-info">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase">
                            Total Kapasitas
                        </div>
                        <div class="h5 font-weight-bold">
                            {{ $totalKapasitas }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- SISA KAPASITAS --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-left-success">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase">
                            Sisa Kapasitas
                        </div>
                        <div class="h5 font-weight-bold">
                            {{ $sisaKapasitas }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- PERSENTASE TERISI --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-left-warning">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase">
                            Terisi (%)
                        </div>
                        <div class="h5 font-weight-bold">
                            {{ $persentaseTerisi }}%
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Konten -->
        <div class="row">

            <!-- Info Lemari -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Informasi Lemari</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
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
                                    <span
                                        class="badge {{ $lemari->status === 'aktif' ? 'badge-success' : 'badge-secondary' }}">
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

            <!-- Daftar Loker -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <strong>Daftar Loker</strong>
                    </div>
                    <div class="card-body table-responsive">

                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Kode Loker</th>
                                    <th>Posisi</th>
                                    <th>Kapasitas</th>
                                    <th>Status</th>
                                    <th width="18%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lemari->lokers as $loker)
                                    <tr>
                                        <td>{{ $loker->kode_loker }}</td>
                                        <td>{{ $loker->kolom }}{{ $loker->baris }}</td>
                                        <td>{{ $loker->kapasitas }}</td>
                                        <td>
                                            <span
                                                class="badge
                                        {{ $loker->status === 'kosong'
                                            ? 'badge-success'
                                            : ($loker->status === 'terisi'
                                                ? 'badge-primary'
                                                : 'badge-danger') }}">
                                                {{ ucfirst($loker->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">

                                                {{-- DETAIL LOKER --}}
                                                <button type="button" class="btn btn-sm btn-info mr-2" title="Detail Loker"
                                                    onclick="showLokerDetail({{ $loker->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                {{-- UPDATE KAPASITAS --}}
                                                <form method="POST"
                                                    action="{{ route('admin.manajemen-lemari.loker.kapasitas', $loker->id) }}"
                                                    class="d-flex align-items-center">
                                                    @csrf
                                                    @method('PUT')

                                                    <input type="number" name="kapasitas"
                                                        class="form-control form-control-sm mr-2" style="width: 80px"
                                                        min="1" value="{{ $loker->kapasitas }}"
                                                        {{ $loker->status === 'terisi' ? 'disabled' : '' }}>

                                                    <button class="btn btn-sm btn-primary" title="Update Kapasitas"
                                                        {{ $loker->status === 'terisi' ? 'disabled' : '' }}>
                                                        <i class="fas fa-save"></i>
                                                    </button>
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
        <div class="modal fade" id="lokerDetailModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="lokerDetailContent">
                    {{-- AJAX CONTENT --}}
                </div>
            </div>
        </div>

        <script>
            function showLokerDetail(lokerId) {
                if (!lokerId) return;

                $('#lokerDetailContent').html(
                    '<div class="p-4 text-center">Memuat data...</div>'
                );

                $('#lokerDetailModal').modal('show');

                $.get(
                    "{{ route('admin.manajemen-lemari.loker.detail', ':id') }}"
                    .replace(':id', lokerId),
                    function(response) {
                        $('#lokerDetailContent').html(response);
                    }
                ).fail(function() {
                    $('#lokerDetailContent').html(
                        '<div class="p-4 text-danger text-center">Gagal memuat data loker</div>'
                    );
                });
            }
        </script>

    </div>
@endsection
