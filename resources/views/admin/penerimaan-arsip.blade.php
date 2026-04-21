@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold text-primary mb-0">
            Penerimaan Arsip
        </h1>

        <span class="badge px-4 py-2 shadow-sm text-white" style="background:#1E3A8A;">
            <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi
        </span>
    </div>

    {{-- ================= FILTER ================= --}}
{{-- ================= FILTER CARD ================= --}}
<div class="card shadow-sm border-0 mb-4">

    <div class="card-header text-white font-weight-bold"
         style="background:linear-gradient(135deg,#38BDF8,#0EA5E9)">
        <i class="fas fa-filter mr-1"></i> Filter Data
    </div>

    <div class="card-body">

        <form method="GET">
            <div class="form-row align-items-end">

                <div class="col-md-4 mb-2">
                    <label class="font-weight-bold">Petugas</label>
                    <select name="petugas" class="form-control custom-height">
                        <option value="">Semua Petugas</option>
                        @foreach($petugasList as $p)
                            <option value="{{ $p->id }}" {{ request('petugas') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8 mb-2 d-flex align-items-end" style="gap:10px;">
                    <button class="btn btn-primary custom-height">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>

                    <a href="{{ route('admin.penerimaan-arsip.index') }}"
                       class="btn btn-outline-secondary custom-height d-flex align-items-center">
                        Reset
                    </a>
                </div>

            </div>
        </form>

    </div>

</div>

    {{-- ================= TABLE CARD ================= --}}
    <div class="card shadow-sm border-0 mb-5">

        <div class="card-header text-white font-weight-bold"
             style="background:linear-gradient(135deg,#38BDF8,#0EA5E9)">
            <i class="fas fa-paper-plane mr-1"></i>
            Daftar Pengiriman Berkas
        </div>

        <div class="card-body">

            <form method="POST"
                  action="{{ route('admin.penerimaan-arsip.bulk') }}"
                  class="form-bulk">

                @csrf

            <div class="mb-3 d-flex justify-content-between align-items-center">

    <div>
        {{-- bisa isi info / text di kiri --}}
    </div>

   {{-- ================= BUTTON BULK ================= --}}
<button type="button" id="btnBulkTerima" class="btn btn-success">
    <i class="fas fa-check mr-1"></i> Terima Dipilih
</button>

</div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">

                        <thead class="thead-light text-center">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th width="50">No</th>
                                <th class="text-left">Kode Permohonan</th>
                                <th width="130">Tanggal</th>
                                <th class="text-left">Asal</th>
                                <th width="150">Petugas</th>
                                <th width="220">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($pengirimanBerkas as $item)
                            <tr>

                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}">
                                </td>

                                <td class="text-center">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="font-weight-bold text-dark">
                                    {{ $item->kode_permohonan }}
                                </td>

                                <td class="text-center">
                                    <span class="badge badge-info px-3 py-2">
                                        {{ $item->tanggal_kirim->format('d/m/Y') }}
                                    </span>
                                </td>

                                <td>{{ $item->asal_berkas }}</td>

                                <td class="text-center">
                                    <span class="badge badge-secondary px-3 py-2">
                                        {{ optional($item->petugasPengirim)->name ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group">

                                        <button type="button"
                                                class="btn btn-success btn-sm btn-terima"
                                                data-id="{{ $item->id }}"
                                                title="Terima">
                                            <i class="fas fa-check"></i>
                                        </button>

                                        <button type="button"
                                                class="btn btn-danger btn-sm btn-tolak"
                                                data-id="{{ $item->id }}"
                                                title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>

                                        <button type="button"
                                                class="btn btn-info btn-sm btn-detail"
                                                data-id="{{ $item->id }}"
                                                title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    Tidak ada data pengiriman
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                {{-- <div class="mt-3">
                    {{ $pengirimanBerkas->links() }}
                </div> --}}

            </form>

        </div>
    </div>

</div>

{{-- ================= MODALS ================= --}}

{{-- PREVIEW --}}
<div class="modal fade" id="modalPreview">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-info shadow">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-archive mr-2"></i> Preview Lokasi Arsip
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="font-weight-bold">Kode Permohonan</label>
                    <div id="previewKode" class="form-control bg-light"></div>
                </div>

                <div class="row text-center">
                    <div class="col-4">
                        <label>Lemari</label>
                        <div id="previewLemari" class="form-control bg-light"></div>
                    </div>
                    <div class="col-4">
                        <label>Loker</label>
                        <div id="previewLoker" class="form-control bg-light"></div>
                    </div>
                    <div class="col-4">
                        <label>Slot</label>
                        <div id="previewSlot" class="form-control bg-light"></div>
                        
                    </div>
                </div>
                 <div id="slotNote" class="border rounded mt-2 px-2 py-1 bg-light" style="display:none;">
                    <small class="text-dark">
                        Arsip akan otomatis menyebar ke slot berikutnya jika penuh
                    </small>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-info" id="btnSimpanBerkas">Simpan</button>
            </div>

        </div>
    </div>
</div>

{{-- TOLAK --}}
<div class="modal fade" id="modalTolak">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formTolak" method="POST">
            @csrf
            <div class="modal-content border-danger shadow">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle mr-2"></i> Tolak Arsip
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <textarea class="form-control"
                              name="alasan_penolakan"
                              rows="4"
                              placeholder="Masukkan alasan penolakan..."
                              required></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-danger">Simpan</button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- DETAIL --}}
<div class="modal fade" id="modalDetail">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-primary shadow">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle mr-2"></i> Detail Pengiriman
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label>Kode Permohonan</label>
                    <div id="detailKode" class="form-control bg-light"></div>
                </div>

                <div class="mb-3">
                    <label>Asal Berkas</label>
                    <div id="detailAsal" class="form-control bg-light"></div>
                </div>

                <div>
                    <label>Status</label>
                    <div id="detailStatus" class="form-control bg-light"></div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>

@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let selectedId = null;   // untuk single
let selectedIds = [];    // untuk bulk


/* =========================
   ✅ TERIMA SINGLE (HIJAU)
   ========================= */
$(document).on('click','.btn-terima', function () {

    selectedId = $(this).data('id');
    selectedIds = []; // reset bulk

    $.get(`{{ url('admin/penerimaan-arsip') }}/${selectedId}/preview`)
    .done(res => {
        let d = res.data;

        $('#previewKode').text(d.kode_permohonan);
        $('#previewLemari').text(d.lemari);
        $('#previewLoker').text(d.loker);
        if (selectedIds.length > 1) {
            $('#previewSlot').html(`Slot awal: <b>${d.slot}</b>`);
            $('#slotNote').show();
        } else {
            $('#previewSlot').text(d.slot);
            $('#slotNote').hide();
        }

        $('#modalPreview').modal('show');
    })
    .fail(() => Swal.fire('Error','Gagal ambil preview','error'));
});


/* =========================
   ✅ TERIMA BULK (DIPILIH)
   ========================= */
$('#btnBulkTerima').click(function () {

    selectedIds = $('input[name="ids[]"]:checked')
        .map(function () {
            return $(this).val();
        }).get();

    if (selectedIds.length === 0) {
        Swal.fire('Pilih minimal 1 data');
        return;
    }

    selectedId = null; // reset single

    let firstId = selectedIds[0];

    $.get(`{{ url('admin/penerimaan-arsip') }}/${firstId}/preview`)
    .done(res => {
        let d = res.data;

        $('#previewKode').text(
            d.kode_permohonan +
            (selectedIds.length > 1 ? ' (+ ' + (selectedIds.length - 1) + ' lainnya)' : '')
        );
        $('#previewLemari').text(d.lemari);
        $('#previewLoker').text(d.loker);
        if (selectedIds.length > 1) {
            $('#previewSlot').html(`Slot awal: <b>${d.slot}</b>`);
            $('#slotNote').show();
        } else {
            $('#previewSlot').text(d.slot);
            $('#slotNote').hide();
        }

        $('#modalPreview').modal('show');
    })
    .fail(() => Swal.fire('Error','Gagal ambil preview','error'));
});


/* =========================
   💾 SIMPAN (SINGLE + BULK)
   ========================= */
$('#btnSimpanBerkas').click(function () {

    // 🔵 BULK
    if (selectedIds.length > 0) {

        $.post(`{{ route('admin.penerimaan-arsip.bulk') }}`, {
            _token: '{{ csrf_token() }}',
            ids: selectedIds
        })
        .done(() => {
            $('#modalPreview').modal('hide');

            Swal.fire({
                icon:'success',
                title:'Berhasil',
                text:'Semua berkas berhasil disimpan',
                timer:1500,
                showConfirmButton:false
            });

            setTimeout(() => location.reload(), 1200);
        })
        .fail(err => Swal.fire('Error', err.responseText, 'error'));
    }

    // 🟢 SINGLE
    else if (selectedId) {

        $.post(`{{ url('admin/penerimaan-arsip') }}/${selectedId}/terima`, {
            _token: '{{ csrf_token() }}'
        })
        .done(() => {
            $('#modalPreview').modal('hide');

            Swal.fire({
                icon:'success',
                title:'Berhasil',
                text:'Berkas berhasil disimpan',
                timer:1500,
                showConfirmButton:false
            });

            setTimeout(() => location.reload(), 1200);
        })
        .fail(err => Swal.fire('Error', err.responseText, 'error'));
    }

});


/* =========================
   📄 DETAIL
   ========================= */
$(document).on('click','.btn-detail', function () {

    let id = $(this).data('id');

    $.get(`/admin/penerimaan-arsip/${id}/detail`)
    .done(res => {
        let d = res.data;

        $('#detailKode').text(d.kode_permohonan);
        $('#detailAsal').text(d.asal_berkas);
        $('#detailStatus').text(d.status);

        $('#modalDetail').modal('show');
    });
});


/* =========================
   ❌ TOLAK
   ========================= */
$(document).on('click','.btn-tolak', function () {

    let id = $(this).data('id');

    $('#formTolak').attr('action', `/admin/penerimaan-arsip/${id}/tolak`);
    $('#modalTolak').modal('show');
});


/* =========================
   ☑️ CHECK ALL
   ========================= */
$('#checkAll').click(function () {
    $('input[name="ids[]"]').prop('checked', this.checked);
});

</script>
@endsection