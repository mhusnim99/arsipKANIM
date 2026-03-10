@extends('layouts.user')

@section('main-content')

    <h3>Detail Berita Acara</h3>

    <p>Nomor: {{ $beritaAcara->nomor_berita_acara }}</p>
    <p>Tanggal: {{ $beritaAcara->tanggal_dibuat }}</p>
    <p>Jumlah Arsip: {{ $beritaAcara->jumlah_arsip }}</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tanggal Kirim</th>
                <th>Asal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($beritaAcara->pengirimanBerkas as $item)
                <tr>
                    <td>{{ $item->kode_permohonan }}</td>
                    <td>{{ $item->tanggal_kirim }}</td>
                    <td>{{ $item->asal_berkas }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('user.berita-acara.pdf', $beritaAcara->id) }}" class="btn btn-success">Download PDF</a>
@endsection
