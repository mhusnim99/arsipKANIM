@extends('layouts.admin')

@section('main-content')
    <h1 class="h3 mb-4 text-gray-800">
        Detail Arsip – {{ $arsip->nomor_arsip }}
    </h1>

    <span class="badge badge-{{ $arsip->status_badge }}">
        {{ $arsip->status_text }}
    </span>
    <table class="table table-bordered">
        <tr>
            <th width="30%">Kode Permohonan</th>
            <td>{{ $arsip->kode_permohonan }}</td>
        </tr>
        <tr>
            <th>Asal Berkas</th>
            <td>{{ $arsip->asal_berkas }}</td>
        </tr>
        <tr>
            <th>Tanggal Masuk</th>
            <td>{{ $arsip->tanggal_masuk->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Petugas Penerima</th>
            <td>{{ optional($arsip->petugasPenerima)->name ?? '-' }}</td>
        </tr>
    </table>
    <div class="alert alert-info">
        <strong>Lokasi Arsip:</strong><br>
        Lemari : {{ $arsip->lemari->kode_lemari }}<br>
        Loker : {{ $arsip->loker->kolom }}{{ $arsip->loker->baris }}
    </div>
    <table class="table table-sm">
        <tr>
            <th>Status</th>
            <td>{{ $arsip->status_text }}</td>
        </tr>
        <tr>
            <th>Diterima Pada</th>
            <td>{{ $arsip->created_at->format('d/m/Y H:i') }}</td>
        </tr>
    </table>
    <div class="mt-4">

        @if ($arsip->status === 'tersimpan')
            <form method="POST" action="{{ route('admin.arsip.pinjam', $arsip) }}" class="d-inline">
                @csrf
                <button class="btn btn-warning">
                    Pinjam Arsip
                </button>
            </form>

            <form method="POST" action="{{ route('admin.arsip.musnah', $arsip) }}" class="d-inline"
                onsubmit="return confirm('Yakin memusnahkan arsip?')">
                @csrf
                <button class="btn btn-danger">
                    Musnahkan
                </button>
            </form>
        @endif

        <a href="{{ route('admin.arsip.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>
@endsection
