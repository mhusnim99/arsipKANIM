@extends('layouts.admin')

@section('main-content')
<div class="container-fluid mb-5">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
         <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Manajemen Arsip
        </h1>
    </div>

    {{-- SEARCH & FILTER --}}
    <div class="card shadow-sm border-0 mb-4 rounded-3" style="background: linear-gradient(135deg, #E0F2FE, #BFDBFE);">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.arsip.index') }}">
            <div class="row align-items-end g-3">

                <div class="col-md-5">
                    <label class="fw-bold small text-primary">Cari Arsip</label>
                    <input type="text" name="q"
                           class="form-control form-control-sm shadow-sm"
                           placeholder="Kode permohonan / Slot"
                           value="{{ request('q') }}"
                           style="border-radius:8px; background-color:#EFF6FF;">
                </div>

                <div class="col-md-3">
                    <label class="fw-bold small text-primary">Status</label>
                    <select name="status"
                            class="form-control form-control-sm shadow-sm"
                            style="border-radius:8px; background-color:#EFF6FF;">
                        <option value="">-- Semua Status --</option>
                        <option value="tersimpan" {{ request('status') == 'tersimpan' ? 'selected' : '' }}>Tersimpan</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        {{-- <option value="musnah" {{ request('status') == 'musnah' ? 'selected' : '' }}>Musnah</option> --}}
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="d-block mb-1">&nbsp;</label> {{-- Spacer supaya sejajar --}}
                    <div class="d-flex justify-content-start gap-2">
                        <button class="btn btn-primary btn-sm shadow-sm" style="border-radius:8px; min-width:120px;">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                        <a href="{{ route('admin.arsip.index') }}"
                           class="btn btn-secondary btn-sm shadow-sm"
                           style="border-radius:8px; background-color:#E2E8F0; color:#1E3A8A; min-width:120px; border:none;">
                            Reset
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>



    {{-- TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white font-weight-bold">
            <i class="fas fa-table mr-2"></i> Daftar Arsip
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered text-center small">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th width="22%">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($arsips as $arsip)
                    @php
    $isTerlambat = $arsip->status === 'dipinjam'
        && $arsip->tanggal_pinjam
        && \Carbon\Carbon::parse($arsip->tanggal_pinjam)->diffInDays(now()) >= 3;
@endphp

{{-- @php
    $isTerlambat = $arsip->status === 'dipinjam';
@endphp --}}
                        <tr class="{{ $isTerlambat ? 'table-danger' : '' }}">
                            <td>{{ $loop->iteration + ($arsips->currentPage() - 1) * $arsips->perPage() }}</td>
                            <td class="font-weight-bold">{{ $arsip->kode_permohonan }}</td>
                            <td><span class="badge badge-light border">{{ $arsip->nomor_arsip }}</span></td>



                            <td>{{ $arsip->tanggal_masuk->format('d/m/Y') }}</td>

                            <td>
    <span class="badge badge-{{ $arsip->status_badge }} px-3 py-1">
        {{ $arsip->status_text }}
    </span>

    @if($isTerlambat)
        <div class="text-danger small mt-1">
            ⚠️ Telat > 3 hari
        </div>
    @endif
</td>

                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('admin.arsip.show', $arsip->id) }}"
                                       class="btn btn-info btn-sm mr-1 px-3">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($arsip->status !== 'musnah' || auth()->user()->role === 'admin')
                                    <div class="dropdown">
                                        <button class="btn btn-secondary btn-sm dropdown-toggle px-3"
                                                type="button"
                                                data-toggle="dropdown">
                                            Status
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                                            <button class="dropdown-item btn-status" data-id="{{ $arsip->id }}" data-status="tersimpan">
                                                <i class="fas fa-box text-success mr-1"></i> Tersimpan
                                            </button>
                                            <button class="dropdown-item btn-status" data-id="{{ $arsip->id }}" data-status="dipinjam">
                                                <i class="fas fa-hand-holding text-warning mr-1"></i> Dipinjam
                                            </button>
                                            {{-- <button class="dropdown-item btn-status" data-id="{{ $arsip->id }}" data-status="musnah">
                                                <i class="fas fa-trash text-danger mr-1"></i> Musnah
                                            </button> --}}
                                        </div>
                                    </div>
</div>
@endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                                Tidak ada data arsip
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $arsips->links() }}
            </div>
        </div>
    </div>

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

{{-- MODAL MUSNAH
<div class="modal fade" id="modalMusnah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="formMusnah">
            @csrf
            @method('PUT')

            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-trash mr-2"></i> Form Pemusnahan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="status" value="musnah">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Dimusnahkan Oleh</label>
                            <input type="text" name="dimusnahkan_oleh" class="form-control" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Tanggal & Waktu</label>
                            <input type="datetime-local" name="tanggal_musnah" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger px-4">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div> --}}
@endsection


@section('scripts')
<script>
function setDateTimeNow(inputName) {
    const now = new Date();
    const y = now.getFullYear();
    const m = String(now.getMonth()+1).padStart(2,'0');
    const d = String(now.getDate()).padStart(2,'0');
    const h = String(now.getHours()).padStart(2,'0');
    const i = String(now.getMinutes()).padStart(2,'0');
    document.querySelector(`input[name="${inputName}"]`).value = `${y}-${m}-${d}T${h}:${i}`;
}

$(document).on('click','.btn-status',function(){
    let id     = $(this).data('id');
    let status = $(this).data('status');

    if(status === 'dipinjam'){
        $('#formPinjam').attr('action',`/admin/arsip/${id}/status`);
        setDateTimeNow('tanggal_pinjam');
        $('#modalPinjam').modal('show');
    }

    // if(status === 'musnah'){
    //     $('#formMusnah').attr('action',`/admin/arsip/${id}/status`);
    //     setDateTimeNow('tanggal_musnah');
    //     $('#modalMusnah').modal('show');
    // }
});
</script>
@endsection
