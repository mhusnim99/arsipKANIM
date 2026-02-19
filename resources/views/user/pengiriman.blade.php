@extends('layouts.user')

@section('main-content')
<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Pengiriman Arsip
        </h1>

        <span class="badge px-4 py-2 shadow-sm" style="background:#1E3A8A; color:white;">
            <i class="fas fa-upload mr-1"></i> Kirim Berkas
        </span>
    </div>

    {{-- ================= CARD FORM ================= --}}
    <div class="card shadow border-0 mb-4">

        <div class="card-header text-white font-weight-bold" style="background:linear-gradient(135deg,#38BDF8,#0EA5E9)">
            <i class="fas fa-edit mr-1"></i> Form Pengiriman Berkas
        </div>

        <div class="card-body">

            {{-- ALERT --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <form action="{{ route('user.pengiriman.store') }}" method="POST">
                @csrf

                <div class="row">

                    {{-- KODE PERMOHONAN --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Kode Permohonan</label>
                            <input type="text"
                                   name="kode_permohonan"
                                   class="form-control @error('kode_permohonan') is-invalid @enderror"
                                   placeholder="PMH-2024-001"
                                   value="{{ old('kode_permohonan') }}"
                                   required>
                            @error('kode_permohonan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- TANGGAL --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Tanggal Kirim</label>
                            <input type="date"
                                   id="tanggal_kirim"
                                   name="tanggal_kirim"
                                   class="form-control @error('tanggal_kirim') is-invalid @enderror"
                                   value="{{ old('tanggal_kirim', date('Y-m-d')) }}"
                                   required>
                            @error('tanggal_kirim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ASAL BERKAS --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Asal Berkas</label>
                            <select name="asal_berkas"
                                    class="form-control @error('asal_berkas') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Asal --</option>
                                @foreach ([
                                    'KANIM',
                                    'ULP LTSA MPP Sidoarjo',
                                    'Immigration Lounge Ciputra World Surabaya',
                                    'ULP Bendul Merisi',
                                    'ULP Wiyung',
                                    'ULP BG Junction',
                                    'ULP Mojokerto'
                                ] as $asal)
                                    <option value="{{ $asal }}" {{ old('asal_berkas') == $asal ? 'selected' : '' }}>
                                        {{ $asal }}
                                    </option>
                                @endforeach
                            </select>
                            @error('asal_berkas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>

                {{-- CATATAN --}}
                <div class="form-group">
                    <label class="font-weight-bold">Catatan Tambahan</label>
                    <textarea name="catatan"
                              rows="3"
                              class="form-control"
                              placeholder="Masukkan catatan jika ada...">{{ old('catatan') }}</textarea>
                </div>

                {{-- BUTTON --}}
                <div class="text-right mt-4">
                    <button type="submit"
                            class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Sekarang
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal_kirim').max = today;
    });
</script>
@endsection

@endsection
