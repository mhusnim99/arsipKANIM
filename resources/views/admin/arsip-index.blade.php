@extends('layouts.admin')

@section('main-content')
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-archive mr-2"></i>Manajemen Arsip
            </h1>
        </div>

        {{-- SEARCH & FILTER --}}
        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.arsip.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="font-weight-bold">Cari Arsip</label>
                            <input type="text" name="q" class="form-control"
                                placeholder="Kode permohonan / Nomor arsip" value="{{ request('q') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="font-weight-bold">Status</label>
                            <select name="status" class="form-control">
                                <option value="">-- Semua Status --</option>
                                <option value="tersimpan" {{ request('status') == 'tersimpan' ? 'selected' : '' }}>
                                    Tersimpan
                                </option>
                                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                                    Dipinjam
                                </option>
                                <option value="hilang" {{ request('status') == 'hilang' ? 'selected' : '' }}>
                                    Hilang
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <button class="btn btn-primary mr-2">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                            <a href="{{ route('admin.arsip.index') }}" class="btn btn-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL ARSIP --}}
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <strong>Daftar Arsip</strong>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode Permohonan</th>
                            <th>Nomor Arsip</th>
                            <th>Lokasi</th>
                            <th>Tanggal Masuk</th>
                            <th>Status</th>
                            <th width="18%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($arsips as $arsip)
                            <tr>
                                <td>{{ $loop->iteration + ($arsips->currentPage() - 1) * $arsips->perPage() }}</td>

                                <td>
                                    <strong>{{ $arsip->kode_permohonan }}</strong>
                                </td>

                                <td>
                                    <code>{{ $arsip->nomor_arsip }}</code>
                                </td>

                                <td>
                                    @if ($arsip->lemari && $arsip->loker)
                                        {{ $arsip->lemari->kode_lemari }}
                                        /
                                        {{ $arsip->loker->kolom }}{{ $arsip->loker->baris_formatted }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $arsip->tanggal_masuk->format('d/m/Y') }}
                                </td>

                                <td>
                                    <span class="badge badge-{{ $arsip->status_badge }}">
                                        {{ $arsip->status_text }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('admin.arsip.show', $arsip->id) }}" class="btn btn-info btn-sm">
                                        Detail
                                    </a>


                                    @if ($arsip->status === 'tersimpan')
                                        <button class="btn btn-warning btn-sm" disabled>
                                            Pinjam
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Tidak ada arsip ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $arsips->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
