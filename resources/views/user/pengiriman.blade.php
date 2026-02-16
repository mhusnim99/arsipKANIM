@extends('layouts.user')

@section('main-content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-paper-plane mr-2"></i>Pengiriman Berkas Paspor
    </h1>

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
                @if ($permohonan['alurterakhir'] === 'SELESAI')
                    <form method="POST" action="{{ route('user.pengiriman.store') }}">
                        @csrf
                        <input type="hidden" name="kode_permohonan"
                               value="{{ $permohonan['nopermohonan'] }}">

                        <button class="btn btn-success">
                            <i class="fas fa-paper-plane mr-1"></i> Kirim Berkas
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary"
                            onclick="alert('Proses belum SELESAI di SIMKIM. Silakan selesaikan terlebih dahulu.')">
                        Kirim Berkas
                    </button>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
