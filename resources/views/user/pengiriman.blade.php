@extends('layouts.user')

@section('main-content')
<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Pengiriman Arsip
        </h1>

        <span class="badge px-4 py-2 shadow-sm" style="background:#1E3A8A; color:white;">
            <i class="fas fa-upload mr-1"></i> Kirim Berkas
        </span>
    </div>
    <div class="container-fluid">
    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- ================= FORM CARI SIMKIM ================= --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Cari Data Permohonan (SIMKIM)</strong>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('user.pengiriman.fetch') }}">
                @csrf

                <div class="form-group">
                    <label>Kode Permohonan</label>
                    <input type="text"
                           name="kode_permohonan"
                           class="form-control"
                           placeholder="Contoh: 2928000011968861"
                           required>
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-search mr-1"></i> Cari Data
                </button>
            </form>
        </div>
    </div>

    {{-- ================= HASIL DATA SIMKIM ================= --}}
    @if (session('simkim'))
        @php
            $data = session('simkim');
            $permohonan = $data['permohonan'];
        @endphp

        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <strong>Detail Permohonan</strong>
            </div>

            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Kode Permohonan</th>
                        <td>{{ $permohonan['nopermohonan'] }}</td>
                    </tr>
                    <tr>
                        <th>Nama Lengkap</th>
                        <td>{{ $permohonan['nama_lengkap'] }}</td>
                    </tr>
                    <tr>
                        <th>No Paspor</th>
                        <td>{{ $permohonan['nopaspor'] }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Permohonan</th>
                        <td>{{ $permohonan['tanggal_permohonan'] }}</td>
                    </tr>
                    <tr>
                        <th>UPT</th>
                        <td>{{ $data['upt']['nama'] }}</td>
                    </tr>
                    <tr>
                        <th>Status Proses</th>
                        <td>
                            <span class="badge badge-info">
                                {{ $permohonan['alurterakhir'] }}
                            </span>
                        </td>
                    </tr>
                </table>

                {{-- ================= TOMBOL KIRIM ================= --}}
                <form method="POST" action="{{ route('user.pengiriman.store') }}">
                    @csrf
                    <input type="hidden" name="kode_permohonan"
                        value="{{ $permohonan['nopermohonan'] }}">

                    <input type="hidden" name="simkim_snapshot"
                       value='@json($data)'>
                    <button class="btn btn-success">
                        <i class="fas fa-paper-plane mr-1"></i> Kirim Berkas
                    </button>
                </form>
            </div>
        </div>
    @endif
        {{-- <h4>Data Hasil Sinkronisasi Otomatis</h4> --}}

    @if(isset($syncData) && $syncData->count())
        <div class="card shadow mt-4">
            <div class="card-header bg-secondary text-white">
                <strong>Data Hasil Sinkronisasi Otomatis</strong>
            </div>

            <div class="card-body">
                @if($syncData->count())
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Kode Permohonan</th>
                                    <th>Status</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($syncData as $item)
                                    <tr>
                                        <td>{{ $item->kode_permohonan }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $item->status_proses }}
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST"
                                                action="{{ route('user.pengiriman.sync', $item->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-success">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Tidak ada data sinkronisasi tersedia.</p>
                @endif
            </div>
        </div>
    @endif
</div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal_kirim').max = today;
    });
</script>
@endsection

@endsection
