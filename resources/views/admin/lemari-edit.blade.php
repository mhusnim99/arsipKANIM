@extends('layouts.admin')

@section('title', 'Edit Lemari')

@section('main-content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold text-primary">
            <i class="fas fa-edit mr-2"></i>Edit Lemari
        </h1>

        <span class="badge badge-pill badge-warning px-4 py-2 shadow-sm">
            <i class="fas fa-archive mr-1"></i> {{ $lemari->kode_lemari }}
        </span>
    </div>

    <!-- CARD FORM -->
    <div class="card shadow border-0 col-lg-8 mx-auto">
        <div class="card-header text-white font-weight-bold"
             style="background:linear-gradient(135deg,#667eea,#764ba2)">
            <i class="fas fa-cog mr-1"></i> Form Edit Lemari
        </div>

        <div class="card-body px-4 py-4">

            <form action="{{ route('admin.manajemen-lemari.update', $lemari->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    {{-- KODE LEMARI --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Kode Lemari</label>
                            <input type="text"
                                   class="form-control bg-light font-weight-bold text-primary"
                                   value="{{ $lemari->kode_lemari }}"
                                   readonly>
                        </div>
                    </div>

                    {{-- STATUS --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Status</label>
                            <input type="text"
                                   class="form-control bg-light font-weight-bold text-{{ $lemari->status === 'penuh' ? 'warning' : 'success' }}"
                                   value="{{ $lemari->status === 'penuh' ? 'Penuh' : 'Aktif' }}"
                                   readonly>
                        </div>
                    </div>

                </div>

                {{-- NAMA LEMARI --}}
                <div class="form-group">
                    <label class="font-weight-bold">Nama Lemari</label>
                    <input type="text"
                           name="nama_lemari"
                           class="form-control @error('nama_lemari') is-invalid @enderror"
                           value="{{ old('nama_lemari', $lemari->nama_lemari) }}"
                           placeholder="Masukkan nama lemari"
                           required>

                    @error('nama_lemari')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- KETERANGAN --}}
                <div class="form-group">
                    <label class="font-weight-bold">Keterangan</label>
                    <textarea name="keterangan"
                              rows="4"
                              class="form-control @error('keterangan') is-invalid @enderror"
                              placeholder="Tambahkan keterangan (opsional)">{{ old('keterangan', $lemari->keterangan) }}</textarea>

                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- BUTTON --}}
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.manajemen-lemari.index') }}"
                       class="btn btn-secondary px-4 mr-2 shadow-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
