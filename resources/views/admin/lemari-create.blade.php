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

                                <div class="form-group col-md-10">
                                    <input type="text"
                                        class="form-control bg-light"
                                        value="Otomatis dibuat oleh sistem"
                                        readonly>
                                </div>

                                @error('kode_lemari')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                        <div class="form-group mt-3">
                            <label>Jumlah Loker <span class="text-danger">*</span></label>

                            <input type="number"
                                name="jumlah_loker"
                                class="form-control"
                                min="1"
                                max="100"
                                value="30"
                                required>

                            <small class="text-muted">
                                Jumlah loker yang akan dibuat otomatis
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Kapasitas per Loker</label>

                            <input type="number"
                                name="kapasitas_default_loker"
                                class="form-control"
                                value="350"
                                min="1"
                                required>

                            <small class="text-muted">
                                Jumlah maksimal arsip dalam 1 loker
                            </small>
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
