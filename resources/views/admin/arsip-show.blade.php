@extends('layouts.admin')

@section('main-content')

<style>
    .header-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
    }

    .header-info {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }

    .header-secondary {
        background: linear-gradient(135deg, #64748b, #475569);
    }

    .header-dark {
        background: linear-gradient(135deg, #1e293b, #0f172a);
    }

    .header-orange {
        background: linear-gradient(135deg, #f97316, #ea580c);
    }

    .header-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .btn-orange {
        background: linear-gradient(135deg, #f97316, #ea580c);
        border: none;
        color: white;
    }

    .btn-orange:hover {
        color: white;
        opacity: .9;
    }

    .table th {
        color: #334155;
        font-weight: 600;
    }

    .card {
        transition: .25s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.12)!important;
    }
</style>

<div class="container-fluid pb-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 font-weight-bold text-dark mb-0">
            <i class="fas fa-folder-open text-primary mr-2"></i>
            Detail Arsip
        </h1>

        <a href="{{ route('admin.arsip.index') }}"
           class="btn btn-outline-primary btn-sm px-3 shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="row">

        {{-- INFORMASI --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">

                <div class="card-header header-primary text-white font-weight-bold">
                    <i class="fas fa-info-circle mr-2"></i>
                    Informasi Arsip
                </div>

                <div class="card-body p-0">
                    <table class="table table-bordered mb-0 small">
                        <tr><th width="35%">Kode Permohonan</th><td>{{ $arsip->kode_permohonan ?? '-' }}</td></tr>
                        <tr><th>Nama Lengkap</th><td>{{ $arsip->nama_lengkap ?? '-' }}</td></tr>
                        <tr><th>Nomor Paspor</th><td>{{ $arsip->nomor_paspor ?? '-' }}</td></tr>
                        <tr><th>Tanggal Permohonan</th><td>{{ optional($arsip->tanggal_permohonan)->format('d/m/Y') ?? '-' }}</td></tr>
                        <tr><th>Status Proses</th><td>{{ $arsip->status_proses ?? '-' }}</td></tr>
                        <tr><th>Asal Berkas</th><td>{{ $arsip->asal_berkas ?? '-' }}</td></tr>
                        <tr><th>Tanggal Masuk</th><td>{{ optional($arsip->tanggal_masuk)->format('d/m/Y') ?? '-' }}</td></tr>
                        <tr><th>Petugas Pengirim</th><td>{{ optional($arsip->petugasPengirim)->name ?? '-' }}</td></tr>
                        <tr><th>Petugas Penerima</th><td>{{ optional($arsip->petugasPenerima)->name ?? '-' }}</td></tr>
                        <tr><th>Diterima Pada</th><td>{{ optional($arsip->created_at)->format('d/m/Y H:i') ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="col-lg-4 mb-4">

            {{-- LOKASI --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header header-info text-white font-weight-bold">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Lokasi Arsip
                </div>

                <div class="card-body text-center py-4">
                    <h4 class="font-weight-bold mb-3">{{ $arsip->nomor_arsip ?? '-' }}</h4>

                    <span class="badge badge-primary px-3 py-2">
                        Lemari {{ optional($arsip->lemari)->kode_lemari ?? '-' }}
                    </span>

                    <span class="badge badge-info px-3 py-2 mx-1">
                        Loker {{ optional($arsip->loker)->kode_loker ?? '-' }}
                    </span>

                    <span class="badge badge-success px-3 py-2">
                        Slot {{ $arsip->slot ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- STATUS --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header header-secondary text-white font-weight-bold">
                    <i class="fas fa-tag mr-2"></i>
                    Status Arsip
                </div>

                <div class="card-body text-center py-4">
                    <span class="badge badge-{{ $arsip->status_badge ?? 'secondary' }} px-4 py-2 shadow-sm">
                        {{ $arsip->status_text ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- AKSI --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header header-dark text-white font-weight-bold">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Aksi Arsip
                </div>

                <div class="card-body text-center">

                    @if($arsip->status !== 'dipinjam')
                        <button class="btn btn-warning px-4 shadow-sm btn-pinjam"
                                data-id="{{ $arsip->id }}">
                            <i class="fas fa-hand-holding mr-1"></i>
                            Dipinjam
                        </button>
                    @endif

                    @if($arsip->status === 'dipinjam')
                        <form method="POST"
                              action="{{ route('admin.arsip.update',$arsip->id) }}"
                              class="d-inline"
                              onsubmit="return confirm('Yakin arsip sudah dikembalikan?')">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="status" value="tersimpan">

                            <button class="btn btn-success px-4 shadow-sm">
                                <i class="fas fa-check mr-1"></i>
                                Arsip Dikembalikan
                            </button>
                        </form>
                    @endif

                </div>
            </div>

            {{-- RIWAYAT --}}
            @if($arsip->histories && $arsip->histories->isNotEmpty())
            <div class="card shadow-sm border-0">
                <div class="card-header header-orange text-white font-weight-bold">
                    <i class="fas fa-history mr-2"></i>
                    Riwayat Peminjaman
                </div>

                <div class="card-body text-center">
                    <button type="button"
                            class="btn btn-orange px-4 shadow-sm"
                            data-toggle="modal"
                            data-target="#modalRiwayat">
                        <i class="fas fa-history mr-1"></i>
                        Lihat Riwayat
                    </button>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- INFO PEMINJAMAN --}}
    @if($arsip->status === 'dipinjam')
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header header-warning text-white font-weight-bold">
            <i class="fas fa-hand-holding mr-2"></i>
            Informasi Peminjaman
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered mb-0 small">
                <tr><th width="30%">Dipinjam Oleh</th><td>{{ $arsip->dipinjam_oleh ?? '-' }}</td></tr>
                <tr><th>Keperluan</th><td>{{ $arsip->keperluan ?? '-' }}</td></tr>
                <tr><th>Tanggal & Waktu</th><td>{{ optional($arsip->tanggal_pinjam)->format('d/m/Y H:i') ?? '-' }}</td></tr>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- MODAL PINJAM --}}
<div class="modal fade" id="modalPinjam" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="formPinjam">
            @csrf
            @method('PUT')

            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-hand-holding mr-2"></i> Form Peminjaman</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="status" value="dipinjam">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Dipinjam Oleh</label>
                            <input type="text" name="dipinjam_oleh" class="form-control" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Tanggal & Waktu</label>
                            <input type="datetime-local" name="tanggal_pinjam" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Keperluan</label>
                        <textarea name="keperluan" class="form-control" rows="3" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-warning px-4">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL RIWAYAT --}}
@if($arsip->histories && $arsip->histories->isNotEmpty())
<div class="modal fade" id="modalRiwayat" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">

            {{-- HEADER --}}
            <div class="modal-header text-white"
                 style="background:linear-gradient(135deg,#C05621,#DD6B20);">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-history mr-2"></i>
                    Riwayat Peminjaman Arsip
                </h5>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- BODY --}}
            <div class="modal-body bg-light p-4">

                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">

                                <thead style="background:#FFF7ED;">
                                    <tr class="text-center">
                                        <th width="6%">No</th>
                                        <th>Kode</th>
                                        <th>Peminjam</th>
                                        <th>Keperluan</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($arsip->histories as $history)
                                    <tr>
                                        <td class="text-center font-weight-bold">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>
                                            {{ $history->kode_permohonan }}
                                        </td>

                                        <td>
                                            {{ $history->peminjam }}
                                        </td>

                                        <td>
                                            {{ $history->keperluan }}
                                        </td>

                                        <td>
                                            {{ optional($history->tanggal_pinjam)->format('d/m/Y H:i') }}
                                        </td>

                                        <td class="text-center">
                                            @if($history->tanggal_kembali)
                                                 <span class="badge badge-success px-3 py-2">
                                                    {{ \Carbon\Carbon::parse($history->tanggal_kembali)
                                                        ->timezone('Asia/Jakarta')
                                                        ->format('d/m/Y H:i') }}
                                                </span>
                                            @else
                                                <span class="badge badge-warning px-3 py-2">
                                                    Belum Dikembalikan
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer bg-white border-0">
                <button type="button"
                        class="btn btn-secondary px-4"
                        data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
@endif

@endsection


@section('scripts')
<script>
$(document).ready(function () {

    function setDateTimeNow() {
        const now = new Date();

        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        const d = String(now.getDate()).padStart(2, '0');
        const h = String(now.getHours()).padStart(2, '0');
        const i = String(now.getMinutes()).padStart(2, '0');

        $('input[name="tanggal_pinjam"]').val(`${y}-${m}-${d}T${h}:${i}`);
    }

    $('.btn-pinjam').on('click', function () {
        const id = $(this).data('id');

        $('#formPinjam').attr('action', `/admin/arsip/${id}/status`);

        setDateTimeNow();

        $('#modalPinjam').modal('show');
    });

});
</script>
@endsection
