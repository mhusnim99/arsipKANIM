@extends('layouts.admin')

@section('title', 'Edit Lemari')

@section('main-content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Edit Lemari</h1>

    <div class="card shadow mb-4">
        <div class="card-body">

            <form action="{{ route('admin.manajemen-lemari.update', $lemari->id) }}"
                  method="POST">
                @csrf
                @method('PUT')

                {{-- Kode Lemari (READ ONLY) --}}
                <div class="form-group">
                    <label>Kode Lemari</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $lemari->kode_lemari }}"
                           readonly>
                </div>

                {{-- Nama Lemari --}}
                <div class="form-group">
                    <label>Nama Lemari</label>
                    <input type="text"
                           name="nama_lemari"
                           class="form-control @error('nama_lemari') is-invalid @enderror"
                           value="{{ old('nama_lemari', $lemari->nama_lemari) }}"
                           required>

                    @error('nama_lemari')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label>Status Lemari</label>
                    <select name="status"
                            class="form-control @error('status') is-invalid @enderror"
                            required>
                        <option value="aktif"
                            {{ old('status', $lemari->status) === 'aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="nonaktif"
                            {{ old('status', $lemari->status) === 'nonaktif' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                    </select>

                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan"
                              rows="3"
                              class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $lemari->keterangan) }}</textarea>

                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.manajemen-lemari.index') }}"
                       class="btn btn-secondary mr-2">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
