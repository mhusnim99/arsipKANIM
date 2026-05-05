@extends('layouts.admin')

@section('main-content')
<div class="container-fluid pb-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-folder-open text-primary mr-2"></i>
            Detail Arsip
        </h1>

        <a href="{{ route('admin.arsip.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>


    <div class="row">

        {{-- INFORMASI UTAMA --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-gradient-primary text-white font-weight-bold">
                    <i class="fas fa-info-circle mr-2"></i>
                    Informasi Arsip
                </div>

                <div class="card-body p-0">
                    <table class="table table-bordered mb-0 small">

                        <tr>
                            <th width="35%" class="bg-light">Kode Permohonan</th>
                            <td class="font-weight-bold">{{ $arsip->kode_permohonan }}</td>
                        </tr>

                        <tr>
                            <th class="bg-light">Nama Lengkap</th>
                            <td>{{ $arsip->nama_lengkap ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th class="bg-light">Nomor Paspor</th>
                            <td>{{ $arsip->nomor_paspor ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th class="bg-light">Tanggal Permohonan</th>
                            <td>{{ optional($arsip->tanggal_permohonan)->format('d/m/Y') }}</td>
                        </tr>

                        <tr>
                            <th class="bg-light">Status Proses</th>
                            <td>{{ $arsip->status_proses ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th class="bg-light">Asal Berkas</th>
                            <td>{{ $arsip->asal_berkas ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th class="bg-light">Tanggal Masuk</th>
                            <td>{{ optional($arsip->tanggal_masuk)->format('d/m/Y') }}</td>
                        </tr>

                        <tr>
                            <th class="bg-light">Petugas Pengirim</th>
                            <td>{{ optional($arsip->petugasPengirim)->name ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th class="bg-light">Petugas Penerima</th>
                            <td>{{ optional($arsip->petugasPenerima)->name ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th class="bg-light">Diterima Pada</th>
                            <td>{{ optional($arsip->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>

                    </table>
                </div>

            </div>
        </div>


        {{-- LOKASI & STATUS --}}
        <div class="col-lg-4 mb-4">

            {{-- LOKASI --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-info text-white font-weight-bold">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Lokasi Arsip
                </div>

                <div class="card-body text-center py-4">
                <h3 class="font-weight-bold mb-2">
                    {{ $arsip->nomor_arsip ?? '-' }}
                </h3>

                <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">

                    {{-- Lemari --}}
                    <span class="badge badge-primary px-3 py-2">
                        Lemari {{ $arsip->lemari->kode_lemari }}
                    </span>

                    <span class="mx-1 text-muted">/</span>

                    {{-- Loker --}}
                    <span class="badge badge-info px-3 py-2">
                        Loker {{ $arsip->loker->kode_loker }}
                    </span>

                    <span class="mx-1 text-muted">/</span>

                    {{-- Slot --}}
                    <span class="badge badge-success px-3 py-2">
                        Slot {{ $arsip->slot }}
                    </span>

                </div>
            </div>

            {{-- STATUS --}}
            <div class="card shadow-sm border-0">

                <div class="card-header bg-secondary text-white font-weight-bold">
                    <i class="fas fa-tag mr-2"></i>
                    Status Arsip
                </div>

                <div class="card-body text-center py-4">
                    <span class="badge badge-{{ $arsip->status_badge }} px-4 py-2 shadow-sm">
                        {{ $arsip->status_text }}
                    </span>
                </div>

            </div>

        </div>

    </div>


    {{-- INFO PEMINJAMAN --}}
    @if ($arsip->status === 'dipinjam')
    <div class="card border-0 shadow-sm mt-4 mb-4">

        <div class="card-header bg-warning text-white font-weight-bold">
            <i class="fas fa-hand-holding mr-2"></i>
            Informasi Peminjaman
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered mb-0 small">

                <tr>
                    <th width="30%" class="bg-light">Dipinjam Oleh</th>
                    <td>{{ $arsip->dipinjam_oleh ?? '-' }}</td>
                </tr>

                <tr>
                    <th class="bg-light">Keperluan</th>
                    <td>{{ $arsip->keperluan ?? '-' }}</td>
                </tr>

                <tr>
                    <th class="bg-light">Tanggal & Waktu</th>
                    <td>{{ optional($arsip->tanggal_pinjam)->format('d/m/Y H:i') }}</td>
                </tr>

            </table>
        </div>

        <div class="card-footer text-right bg-light">
            <form method="POST"
                  action="{{ route('admin.arsip.update', $arsip->id) }}"
                  onsubmit="return confirm('Yakin arsip sudah dikembalikan?')">

                @csrf
                @method('PUT')

                <input type="hidden" name="status" value="tersimpan">

                <button class="btn btn-success px-4">
                    <i class="fas fa-check mr-1"></i>
                    Arsip Dikembalikan
                </button>

            </form>
        </div>

    </div>
    @endif


    {{-- INFO PEMUSNAHAN --}}
    @if ($arsip->status === 'musnah')
    <div class="card border-0 shadow-sm mt-4 mb-4">

        <div class="card-header bg-danger text-white font-weight-bold">
            <i class="fas fa-trash mr-2"></i>
            Informasi Pemusnahan
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered mb-0 small">

                <tr>
                    <th width="30%" class="bg-light">Dimusnahkan Oleh</th>
                    <td>{{ $arsip->dimusnahkan_oleh ?? '-' }}</td>
                </tr>

                <tr>
                    <th class="bg-light">Tanggal & Waktu</th>
                    <td>{{ $arsip->tanggal_musnah? \Carbon\Carbon::parse($arsip->tanggal_musnah)->format('d/m/Y H:i'): '-' }}</td>
                </tr>

            </table>
        </div>

    </div>
    @endif

</div>
@endsection
