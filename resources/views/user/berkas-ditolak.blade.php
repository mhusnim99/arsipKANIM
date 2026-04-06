@extends('layouts.user')

@section('main-content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Berkas Perlu Perbaikan
        </h1>
        <span class="badge px-4 py-2 shadow-sm" style="background:#1E3A8A; color:white;">
            <i class="fas fa-exclamation-triangle"></i> Ditolak
        </span>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- CARD TABLE -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header text-white font-weight-bold"
             style="background:linear-gradient(135deg,#f85032,#e73827)">
            <i class="fas fa-file-alt mr-1"></i> Daftar Berkas Ditolak
        </div>

        <div class="card-body table-responsive">

            @if($pengirimanBerkas->count() == 0)

                <div class="text-center py-5 text-muted">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                    <h5>Tidak ada berkas yang perlu diperbaiki</h5>
                </div>

            @else

                <table class="table table-hover table-bordered align-middle">
                    <thead class="thead-light text-center">
                        <tr>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th width="120">Tanggal</th>
                            <th>Asal</th>
                            <th>Alasan</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach($pengirimanBerkas as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>

                            <td >
                                {{ $item->kode_permohonan }}
                            </td>

                            <td class="text-center">
                                <span class="badge badge-info px-3">
                                    {{ $item->tanggal_kirim->format('d/m/Y') }}
                                </span>
                            </td>

                            <td>{{ $item->asal_berkas }}</td>

                            <td>
                                <span >
                                    {{ $item->alasan_penolakan }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('user.pengiriman.kirim-perbaikan', $item->id) }}"
                                      method="POST"
                                      class="form-kirim d-inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-warning btn-sm px-3 shadow-sm">
                                        <i class="fas fa-paper-plane mr-1"></i>
                                        Kirim Perbaikan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $pengirimanBerkas->links() }}
                </div>

            @endif

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).on('submit', '.form-kirim', function(e) {
    e.preventDefault();

    let form = this;

    Swal.fire({
        title: 'Kirim Perbaikan?',
        text: 'Berkas akan dikirim ulang ke admin untuk diperiksa kembali.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f6c23e',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, kirim',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>
@endsection
