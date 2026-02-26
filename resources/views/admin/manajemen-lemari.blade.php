@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Manajemen Lemari
        </h1>

        <a href="{{ route('admin.manajemen-lemari.create') }}"
           class="badge px-4 py-2 shadow-sm" style="background:#1E3A8A; color:white;">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Lemari
        </a>
    </div>

    <!-- CARD TABLE -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header text-white font-weight-bold"
             style="background:linear-gradient(135deg,#f6d365,#fda085)">
            <i class="fas fa-table mr-1"></i> Daftar Lemari Arsip
        </div>

        <div class="card-body">

            {{-- ALERT --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <i class="fas fa-times-circle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="thead-light text-center">
                        <tr>
                            <th width="50">No</th>
                            <th>Kode Lemari</th>
                            <th>Nama Lemari</th>
                            <th>Total Loker</th>
                            <th>Kapasitas</th>
                            <th>Loker Terisi</th>
                            <th>Status</th>
                            <th width="160">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="text-center">
                        @forelse($lemaris as $lemari)
                            @php
                                $totalLoker = $lemari->lokers->count();
                                $totalKapasitas = $lemari->lokers->sum('kapasitas');
                            @endphp

                            <tr>
                                <td>
                                    {{ ($lemaris->currentPage() - 1) * $lemaris->perPage() + $loop->iteration }}
                                </td>

                                <td >
                                    {{ $lemari->kode_lemari }}
                                </td>

                                <td>{{ $lemari->nama_lemari }}</td>

                                <td>
                                    <span class="badge badge-info px-3">
                                        {{ $totalLoker }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge badge-primary px-3">
                                        {{ $totalKapasitas }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge badge-warning px-3">
                                        {{ $lemari->jumlah_loker_terisi }} / {{ $totalLoker }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge {{ $lemari->status === 'aktif' ? 'badge-success' : 'badge-secondary' }} px-3">
                                        {{ ucfirst($lemari->status) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.manajemen-lemari.show', $lemari->id) }}"
                                           class="btn btn-sm btn-info shadow-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.manajemen-lemari.edit', $lemari->id) }}"
                                           class="btn btn-sm btn-warning shadow-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.manajemen-lemari.destroy', $lemari->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus lemari ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger shadow-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <br>Belum ada data lemari
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="mt-3">
                {{ $lemaris->links('pagination::bootstrap-4') }}
            </div>

        </div>
    </div>

</div>
@endsection
