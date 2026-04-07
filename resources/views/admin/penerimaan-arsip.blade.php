@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
       <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Penerimaan Arsip
        </h1>
        <span class="badge px-4 py-2 shadow-sm" style="background:#1E3A8A; color:white;">
            <i class="fas fa-clock"></i> Menunggu Verifikasi
        </span>
    </div>

    <!-- CARD TABLE -->
    <form method="GET" class="mb-3">
        <div class="d-flex gap-2">

            <select name="petugas" class="form-control">
                <option value="">-- Pilih Petugas --</option>
                @foreach($petugasList as $p)
                    <option value="{{ $p->id }}" {{ request('petugas') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-primary">Filter</button>

            <a href="{{ route('admin.penerimaan-arsip.index') }}" class="btn btn-secondary">
                Reset
            </a>

        </div>
    </form>
    <div class="card shadow border-0 mb-4">
        <div class="card-header text-white font-weight-bold" style="background:linear-gradient(135deg,#38BDF8,#0EA5E9)">
            <i class="fas fa-paper-plane mr-1"></i> Daftar Pengiriman Berkas
        </div>

        <div class="card-body table-responsive">
            <form method="POST" action="{{ route('admin.penerimaan-arsip.bulk') }}" class="form-bulk">
                @csrf
                <button class="btn btn-success mb-3">
                    ✔ Terima yang dipilih
                </button>
                <table class="table table-hover table-bordered align-middle">
                    <thead class="thead-light text-center">
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th width="50">No</th>
                            <th class="text-left">Kode Permohonan</th>
                            <th width="120">Tanggal</th>
                            <th class="text-left">Asal</th>
                            <th width="140">Petugas</th>
                            <th width="230">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengirimanBerkas as $item)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="ids[]" value="{{ $item->id }}">
                            </td>

                            <td class="text-center">
                                {{ $pengirimanBerkas->firstItem() + $loop->index }}
                            </td>
                            <td class="thead-light">{{ $item->kode_permohonan }}</td>
                            <td class="text-center">
                                <span class="badge badge-info px-3">
                                    {{ $item->tanggal_kirim->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>{{ $item->asal_berkas }}</td>
                            <td class="text-center">
                                <span class="badge badge-secondary px-3">
                                    {{ optional($item->petugasPengirim)->name ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-success btn-sm btn-terima px-3 shadow-sm"
                                            data-id="{{ $item->id }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-tolak px-3 shadow-sm"
                                            data-id="{{ $item->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button class="btn btn-info btn-sm btn-detail px-3 shadow-sm"
                                            data-id="{{ $item->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <br>Tidak ada data pengiriman
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $pengirimanBerkas->links() }}
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TERIMA --}}
<div class="modal fade" id="modalTerima" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-success shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle mr-2"></i> Berhasil
                </h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <h5 id="hasilKode"></h5>
                <p id="hasilLokasi"></p>
                <span class="badge badge-success" id="hasilStatus"></span>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW --}}
<div class="modal fade" id="modalPreview" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
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
                    <div id="previewKode" class="border rounded p-2 bg-light"></div>
                </div>

                <hr>

                <div class="row text-center">

                    <div class="col-4">
                        <label class="font-weight-bold">Lemari</label>
                        <div id="previewLemari" class="border rounded p-2 bg-light"></div>
                    </div>

                    <div class="col-4">
                        <label class="font-weight-bold">Loker</label>
                        <div id="previewLoker" class="border rounded p-2 bg-light"></div>
                    </div>

                    <div class="col-4">
                        <label class="font-weight-bold">Slot</label>
                        <div id="previewSlot" class="border rounded p-2 bg-light"></div>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary px-4" data-dismiss="modal">
                    Batal
                </button>

                <button class="btn btn-info px-4" id="btnSimpanBerkas">
                    Simpan Berkas
                </button>
            </div>

        </div>
    </div>
</div>

{{-- MODAL TOLAK --}}
<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <form id="formTolak" method="POST" class="w-100">
            @csrf
            <div class="modal-content border-danger shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle mr-2"></i> Tolak Arsip
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Alasan Penolakan</label>
                        <textarea class="form-control" name="alasan_penolakan"
                                  rows="4" placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-primary shadow">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle mr-2"></i> Detail Pengiriman
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="font-weight-bold">Kode Permohonan</label>
                    <div id="detailKode" class="border rounded p-2 bg-light"></div>
                </div>

                <div class="mb-3">
                    <label class="font-weight-bold">Asal Berkas</label>
                    <div id="detailAsal" class="border rounded p-2 bg-light"></div>
                </div>

                <div class="mb-3">
                    <label class="font-weight-bold">Status</label>
                    <div id="detailStatus" class="border rounded p-2 bg-light"></div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary px-4" data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let selectedId = null;

$(document).on('click','.btn-terima',function(){

    selectedId = $(this).data('id');

    $.get("{{ url('admin/penerimaan-arsip') }}/" + selectedId + "/preview")
    .done(function(res){

        let data = res.data;

        $('#previewKode').text(data.kode_permohonan);
        $('#previewLemari').text(data.lemari);
        $('#previewLoker').text(data.loker);
        $('#previewSlot').text(data.slot);

        $('#modalPreview').modal('show');

    })
    .fail(function(err){
        console.log(err);
        Swal.fire('Error','Preview gagal diambil','error');
    });

});

$(document).on('click','#btnSimpanBerkas',function(){

    $.post("{{ url('admin/penerimaan-arsip') }}/" + selectedId + "/terima",{
        _token:'{{ csrf_token() }}'
    })
    .done(function(){

        $('#modalPreview').modal('hide');

        Swal.fire({
            icon:'success',
            title:'Berhasil',
            text:'Berkas berhasil disimpan',
            timer:1500,
            showConfirmButton:false
        });

        setTimeout(()=>{ location.reload(); },1200);

    })
    .fail(function(err){
        console.log(err);
        Swal.fire('Error','Gagal menyimpan arsip','error');
    });

});
</script>

<script>
   $('.btn-detail').click(function(){

    let id = $(this).data('id');

    $.get(`/admin/penerimaan-arsip/${id}/detail`)
    .done(function(res){

        let data = res.data;

        $('#detailKode').text(data.kode_permohonan);
        $('#detailAsal').text(data.asal_berkas);
        $('#detailStatus').text(data.status);

        $('#modalDetail').modal('show');

    });

});
</script>

<script>
    $('.btn-tolak').click(function(){

    let id = $(this).data('id');

    $('#formTolak').attr(
        'action',
        `/admin/penerimaan-arsip/${id}/tolak`
    );

    $('#modalTolak').modal('show');

});
</script>
<script>
    document.getElementById('checkAll').addEventListener('click', function() {
        let checkboxes = document.querySelectorAll('input[name="ids[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
<script>
$('.form-bulk').submit(function(e){

    let checked = $('input[name="ids[]"]:checked').length;

    if(checked === 0){
        e.preventDefault();
        Swal.fire('Pilih minimal 1 data');
        return;
    }

    e.preventDefault();

    Swal.fire({
        title: 'Yakin?',
        text: 'Terima semua berkas terpilih?',
        icon: 'warning',
        showCancelButton: true
    }).then((result)=>{
        if(result.isConfirmed){
            e.currentTarget.submit();
        }
    });

});
</script>

@endsection
