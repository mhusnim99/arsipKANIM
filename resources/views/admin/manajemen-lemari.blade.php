@extends('layouts.admin')

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Manajemen Lemari Arsip</h1>
            <a href="{{ route('admin.manajemen-lemari.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle mr-2"></i>Tambah Lemari
            </a>
        </div>

        <!-- Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-table mr-2"></i>Daftar Lemari
                </h6>
            </div>

            <div class="card-body">

                {{-- Notifikasi --}}
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Lemari</th>
                                <th>Nama Lemari</th>
                                <th>Total Loker</th>
                                <th>Total Kapasitas</th>
                                <th>Loker Terisi</th>
                                <th>Status</th>
                                <th width="18%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lemaris as $lemari)
                                @php
                                    $totalLoker = $lemari->lokers->count();
                                    $totalKapasitas = $lemari->lokers->sum('kapasitas');
                                    $lokerTerisi = $lemari->lokers->where('status', 'terisi')->count();
                                @endphp
                                <tr>
                                    <td>{{ ($lemaris->currentPage() - 1) * $lemaris->perPage() + $loop->iteration }}</td>
                                    <td><strong>{{ $lemari->kode_lemari }}</strong></td>
                                    <td>{{ $lemari->nama_lemari }}</td>
                                    <td class="text-center">{{ $totalLoker }}</td>
                                    <td class="text-center">{{ $totalKapasitas }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">
                                            {{ $lemari->jumlah_loker_terisi }} / {{ $lemari->lokers->count() }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge {{ $lemari->status == 'aktif' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ ucfirst($lemari->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.manajemen-lemari.show', $lemari->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.manajemen-lemari.edit', $lemari->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.manajemen-lemari.destroy', $lemari->id) }}"
                                            method="POST" class="d-inline" onsubmit="return confirm('Hapus lemari ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Belum ada data lemari
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                {{ $lemaris->links('pagination::bootstrap-4') }}

            </div>
        </div>
    </div>
@endsection
