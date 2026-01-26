@extends('layouts.admin')

@section('main-content')
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tambah Lemari Arsip</h1>
            <a href="{{ route('admin.manajemen-lemari.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <!-- Card -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-plus-circle mr-2"></i>Form Tambah Lemari
                </h6>
            </div>

            <div class="card-body">

                <form method="POST" action="{{ route('admin.manajemen-lemari.store') }}">
                    @csrf

                    {{-- Kode Lemari --}}
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Kode Lemari <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kode_lemari"
                            class="form-control @error('kode_lemari') is-invalid @enderror" value="{{ old('kode_lemari') }}"
                            placeholder="Contoh: L1" required>

                        @error('kode_lemari')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="text-muted">
                            Digunakan sebagai prefix kode loker (contoh: L1.A1.0001)
                        </small>
                    </div>

                    {{-- Nama Lemari --}}
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Nama Lemari <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_lemari"
                            class="form-control @error('nama_lemari') is-invalid @enderror" value="{{ old('nama_lemari') }}"
                            placeholder="Contoh: Lemari Arsip Paspor" required>

                        @error('nama_lemari')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan (Opsional)</label>
                        <textarea name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror"
                            placeholder="Catatan tambahan mengenai lemari">{{ old('keterangan') }}</textarea>

                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Info Sistem --}}
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Informasi Sistem:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Setiap lemari otomatis dibuatkan <strong>30 loker</strong></li>
                            <li>Struktur tetap: <strong>3 kolom × 10 baris</strong></li>
                            <li>Kode loker mengikuti format: <code>L1.A1.0001</code></li>
                            <li>Kapasitas awal tiap loker dapat diubah di halaman <strong>Detail Lemari</strong></li>
                        </ul>
                    </div>

                    {{-- Tombol --}}
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save mr-2"></i>Simpan Lemari
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
