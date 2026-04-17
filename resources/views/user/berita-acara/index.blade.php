@extends('layouts.user')

@section('main-content')

    <h3>Buat Berita Acara</h3>

    <form method="POST" action="{{ route('user.berita-acara.store') }}">
        @csrf

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Pilih</th>
                    <th>Kode</th>
                    <th>Tanggal Kirim</th>
                    <th>Asal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengiriman as $item)
                    <tr>
                        <td>
                            <input type="checkbox" name="pengiriman_ids[]" value="{{ $item->id }}">
                        </td>
                        <td>{{ $item->kode_permohonan }}</td>
                        <td>{{ $item->tanggal_kirim }}</td>
                        <td>{{ $item->asal_berkas }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button class="btn btn-primary">Download Berita Acara</button>
    </form>

@endsection