@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Riwayat Musnah Berkas
        </h1>
    </div>

    {{-- ================= SEARCH ================= --}}
    <div class="card shadow-sm border-0 mb-4 rounded-3"
         style="background: linear-gradient(135deg, #E0F2FE, #BFDBFE);">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.musnah.index') }}">
                <div class="row align-items-end">

                    {{-- Nama --}}
                    <div class="col-md-3 mb-2">
                        <label class="fw-bold small text-primary">Nama</label>
                        <input type="text" name="nama"
                               class="form-control form-control-sm shadow-sm"
                               placeholder="Cari nama"
                               value="{{ request('nama') }}"
                               style="border-radius:8px; background-color:#EFF6FF;">
                    </div>

                    {{-- No Paspor --}}
                    <div class="col-md-3 mb-2">
                        <label class="fw-bold small text-primary">No Paspor</label>
                        <input type="text" name="paspor"
                               class="form-control form-control-sm shadow-sm"
                               placeholder="Cari no paspor"
                               value="{{ request('paspor') }}"
                               style="border-radius:8px; background-color:#EFF6FF;">
                    </div>

                    {{-- Kode Permohonan --}}
                    <div class="col-md-3 mb-2">
                        <label class="fw-bold small text-primary">Kode Permohonan</label>
                        <input type="text" name="kode"
                               class="form-control form-control-sm shadow-sm"
                               placeholder="Cari kode"
                               value="{{ request('kode') }}"
                               style="border-radius:8px; background-color:#EFF6FF;">
                    </div>

                    {{-- Button --}}
                    <div class="col-md-3 mb-2">
                        <label class="d-block mb-1">&nbsp;</label>
                        <div class="d-flex">
                            <button class="btn btn-primary btn-sm shadow-sm mr-2"
                                    style="border-radius:8px; min-width:90px;">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>

                            <a href="{{ route('admin.musnah.index') }}"
                               class="btn btn-secondary btn-sm shadow-sm"
                               style="border-radius:8px; min-width:90px;">
                                Reset
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ================= TABLE ================= --}}
    <form method="POST" action="{{ route('admin.musnah.bulkDelete') }}">
        @csrf
        @method('DELETE')

        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white font-weight-bold d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-trash mr-2"></i> Daftar Berkas Musnah
                </span>

                <button type="submit"
                        class="btn btn-light btn-sm shadow-sm"
                        onclick="return confirm('Yakin hapus data yang dipilih?')">
                    <i class="fas fa-trash mr-1"></i> Hapus Terpilih
                </button>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover table-bordered text-center small">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th width="5%">No</th>
                            <th>Tanggal Musnah</th>
                            <th>No Paspor</th>
                            <th>Nama</th>
                            <th>Kode Permohonan</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($arsips as $arsip)
                        <tr>
                            {{-- Checkbox --}}
                            <td>
                                <input type="checkbox" name="ids[]" value="{{ $arsip->id }}">
                            </td>

                            {{-- Nomor --}}
                            <td>
                                {{ $loop->iteration + ($arsips->currentPage() - 1) * $arsips->perPage() }}
                            </td>

                            {{-- Tanggal --}}
                            <td>
                                {{ $arsip->tanggal_musnah
                                    ? \Carbon\Carbon::parse($arsip->tanggal_musnah)->format('d/m/Y H:i')
                                    : '-' }}
                            </td>

                            {{-- Data --}}
                            <td>{{ $arsip->nomor_paspor }}</td>
                            <td class="font-weight-bold">{{ $arsip->nama_lengkap }}</td>
                            <td>{{ $arsip->kode_permohonan }}</td>

                            {{-- Aksi --}}
                            <td>
                                <a href="{{ route('admin.arsip.show', $arsip->id) }}"
                                   class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-muted py-4">
                                <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                                Tidak ada data musnah
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $arsips->links() }}
                </div>
            </div>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
    // Select All Checkbox
    document.getElementById('checkAll').addEventListener('click', function () {
        let checkboxes = document.querySelectorAll('input[name="ids[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection
