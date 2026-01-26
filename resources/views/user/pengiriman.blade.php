<!-- resources/views/pengiriman/index.blade.php -->
@extends('layouts.user')

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Pengiriman Berkas Paspor</h1>
            <a href="{{ route('user.pengiriman-riwayat') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-history"></i> Lihat Riwayat
            </a>
        </div>

        <!-- Card Pengiriman -->
        <div class="card shadow mb-4" style="border-radius: 10px; border: 1px solid #e3e6f0;">
            <div class="card-header py-3"
                style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border-radius: 10px 10px 0 0;">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-paper-plane mr-2"></i>Form Pengiriman Berkas
                </h6>
            </div>

            <div class="card-body" style="background-color: #f8f9fc;">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert"
                        style="border-left: 4px solid #1cc88a;">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert"
                        style="border-left: 4px solid #e74a3b;">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form action="{{ route('user.pengiriman.store') }}" method="POST" id="formPengiriman">
                    @csrf

                    <div class="row" style="margin-bottom: 20px;">
                        <!-- Kode Permohonan -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kode_permohonan" style="font-weight: 600; color: #5a5c69;">
                                    <i class="fas fa-barcode mr-1"></i> Kode Permohonan
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('kode_permohonan') is-invalid @enderror"
                                    id="kode_permohonan" name="kode_permohonan" value="{{ old('kode_permohonan') }}"
                                    placeholder="Contoh: PMH-2024-001" required
                                    style="border-radius: 8px; border: 1px solid #d1d3e2; padding: 12px; transition: all 0.3s;"
                                    onfocus="this.style.borderColor='#4e73df'; this.style.boxShadow='0 0 0 0.2rem rgba(78, 115, 223, 0.25)';"
                                    onblur="this.style.borderColor='#d1d3e2'; this.style.boxShadow='none';">
                                @error('kode_permohonan')
                                    <div class="invalid-feedback" style="font-size: 0.875em;">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted" style="margin-top: 5px;">
                                    Format: PMH-TAHUN-URUTAN (Contoh: PMH-2024-001)
                                </small>
                            </div>
                        </div>

                        <!-- Tanggal Kirim -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tanggal_kirim" style="font-weight: 600; color: #5a5c69;">
                                    <i class="fas fa-calendar-alt mr-1"></i> Tanggal Kirim
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('tanggal_kirim') is-invalid @enderror"
                                    id="tanggal_kirim" name="tanggal_kirim"
                                    value="{{ old('tanggal_kirim', date('Y-m-d')) }}" required
                                    style="border-radius: 8px; border: 1px solid #d1d3e2; padding: 12px;">
                                @error('tanggal_kirim')
                                    <div class="invalid-feedback" style="font-size: 0.875em;">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Asal Berkas -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="asal_berkas" style="font-weight: 600; color: #5a5c69;">
                                    <i class="fas fa-map-marker-alt mr-1"></i> Asal Berkas
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('asal_berkas') is-invalid @enderror" id="asal_berkas"
                                    name="asal_berkas" required
                                    style="border-radius: 8px; border: 1px solid #d1d3e2; padding: 12px; height: auto;">
                                    <option value="">-- Pilih Asal Berkas --</option>
                                    @foreach (['KANIM', 'ULP LTSA MPP Sidoarjo', 'Immigration Lounge Ciputra World Surabaya', 'ULP Bendul Merisi', 'ULP Wiyung', 'ULP BG Junction', 'ULP Mojokerto'] as $asal)
                                        <option value="{{ $asal }}"
                                            {{ old('asal_berkas') == $asal ? 'selected' : '' }}>
                                            {{ $asal }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('asal_berkas')
                                    <div class="invalid-feedback" style="font-size: 0.875em;">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="row" style="margin-bottom: 25px;">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="catatan" style="font-weight: 600; color: #5a5c69;">
                                        <i class="fas fa-sticky-note mr-1"></i> Catatan (Opsional)
                                    </label>
                                    <textarea class="form-control" id="catatan" name="catatan" rows="3"
                                        placeholder="Tambahkan catatan mengenai berkas..."
                                        style="border-radius: 8px; border: 1px solid #d1d3e2; padding: 12px; resize: vertical;">{{ old('catatan') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Info & Tombol Aksi -->
                        <div class="row">
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary"
                                    style="border-radius: 8px; padding: 12px 30px; font-weight: 600; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';">
                                    <i class="fas fa-paper-plane mr-2"></i> Kirim Berkas
                                </button>
                            </div>
                        </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Script untuk validasi -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formPengiriman');

            // Set tanggal maksimal hari ini
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tanggal_kirim').max = today;

            // Validasi sebelum submit
            form.addEventListener('submit', function(event) {
                const kode = document.getElementById('kode_permohonan').value.trim();
                const tanggal = document.getElementById('tanggal_kirim').value;
                const asal = document.getElementById('asal_berkas').value;

                if (!kode || !tanggal || !asal) {
                    event.preventDefault();
                    alert('Harap isi semua field yang wajib diisi!');
                    return false;
                }

                // Validasi format kode permohonan (opsional)
                const kodeRegex = /^PMH-\d{4}-\d{3}$/;
                if (!kodeRegex.test(kode)) {
                    if (!confirm('Format kode permohonan tidak standar. Lanjutkan?')) {
                        event.preventDefault();
                        return false;
                    }
                }
            });
        });
    </script>

    <style>
        /* Style tambahan untuk responsif */
        @media (max-width: 768px) {
            .row {
                margin-bottom: 10px !important;
            }

            .col-md-4,
            .col-md-8,
            .col-md-12 {
                margin-bottom: 15px;
            }

            .text-right {
                text-align: left !important;
                margin-top: 15px;
            }
        }
    </style>
@endsection
