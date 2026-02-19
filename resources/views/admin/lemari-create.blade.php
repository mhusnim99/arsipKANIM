@extends('layouts.admin')

@section('title', 'Tambah Lemari')

@section('main-content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 text-gray-800 mb-0">
            <i class="fas fa-plus-circle text-primary mr-2"></i>
            Tambah Lemari Arsip
        </h1>

        <a href="{{ route('admin.manajemen-lemari.index') }}"
           class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    {{-- FORM CARD --}}
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <strong>Form Data Lemari</strong>
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('admin.manajemen-lemari.store') }}">
                        @csrf

                        <div class="form-row">

                            {{-- KODE LEMARI --}}
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold">
                                    Kode Lemari <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="kode_lemari"
                                       class="form-control @error('kode_lemari') is-invalid @enderror"
                                       value="{{ old('kode_lemari') }}"
                                       placeholder="Contoh: L1"
                                       required>

                                @error('kode_lemari')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small class="text-muted">
                                    Contoh format: <code>L1.A1.0001</code>
                                </small>
                            </div>

                            {{-- NAMA LEMARI --}}
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold">
                                    Nama Lemari <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="nama_lemari"
                                       class="form-control @error('nama_lemari') is-invalid @enderror"
                                       value="{{ old('nama_lemari') }}"
                                       placeholder="Contoh: Lemari Arsip Paspor"
                                       required>

                                @error('nama_lemari')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        {{-- KETERANGAN --}}
                        <div class="form-group">
                            <label class="font-weight-bold">Keterangan</label>

                            <textarea name="keterangan"
                                      rows="3"
                                      class="form-control @error('keterangan') is-invalid @enderror"
                                      placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>

                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- INFO --}}
                        <div class="alert alert-info small border-left-info">
                            <i class="fas fa-info-circle mr-1"></i>
                            Sistem akan otomatis membuat <strong>30 loker</strong>
                            dengan struktur <strong>3 kolom × 10 baris</strong>.
                        </div>

                        {{-- BUTTON --}}
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit"
                                    class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-save mr-2"></i> Simpan
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection
