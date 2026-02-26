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
    <div class="card shadow border-0 mb-4">
        <div class="card-header text-white font-weight-bold" style="background:linear-gradient(135deg,#38BDF8,#0EA5E9)">
            <i class="fas fa-paper-plane mr-1"></i> Daftar Pengiriman Berkas
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="thead-light text-center">
                    <tr>
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
                        <td class="text-center">{{ $loop->iteration }}</td>
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
                        <td colspan="6" class="text-center text-muted py-4">
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
<div class="modal fade" id="modalDetail">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5>Detail Pengiriman</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body" id="detailContent">Loading...</div>
                </div>
            </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function() {

    $('.btn-terima').click(function(){
        let id = $(this).data('id');

        $.post(`/admin/penerimaan-arsip/${id}/terima`,
    {_token:'{{ csrf_token() }}'}).done(function(res){
    Swal.fire({
        icon:'success',
        title:'Berhasil',
        text:'Berkas berhasil diterima',
        timer:1500,
        showConfirmButton:false
    });
    setTimeout(()=>location.reload(),1200);}).fail(function(xhr){
    Swal.fire({
        icon:'error',
        title:'Terjadi Kesalahan',
        text: xhr.responseJSON?.message ?? 'Server Error'});});
    });

     $(document).on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            $('#modalDetail').modal('show');
            $('#detailContent').html('Loading...');
            $.get(`/admin/penerimaan-arsip/${id}/detail`, res => {
                $('#detailContent').html(`
            <table class="table table-bordered">
                <tr><th>Kode</th><td>${res.data.kode_permohonan}</td></tr>
                <tr><th>Asal</th><td>${res.data.asal_berkas}</td></tr>
                <tr><th>Status</th><td>${res.data.status}</td></tr>
            </table>
        `);
            });
    });

    $('.btn-tolak').click(function(){
        $('#formTolak').data('id', $(this).data('id'));
        $('#modalTolak').modal('show');
    });

    $('#formTolak').submit(function(e){
        e.preventDefault();
        let id = $(this).data('id');

        $.post(`/admin/penerimaan-arsip/${id}/tolak`, $(this).serialize(), function(){
            $('#modalTolak').modal('hide');
            Swal.fire({
                icon:'success',
                title:'Ditolak',
                text:'Berkas berhasil ditolak',
                timer:1500,
                showConfirmButton:false
            });
            setTimeout(()=>location.reload(),1200);
        });
    });

});
</script>
@endsection
