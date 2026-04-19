@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Menu Musnah Berkas
        </h1>
    </div>

    @if (!isset($tahun))

        <!-- MODE FOLDER -->
        <div class="row">
            @forelse ($years as $th)
                <div class="col-md-3 mb-4">
                    <div class="card shadow border-0 text-center p-4">

                        <i class="fas fa-folder fa-3x mb-3" style="color:#f6c23e;"></i>

                        <h5 class="font-weight-bold mb-2">
                            Berkas {{ $th }}
                        </h5>

                        <p class="text-muted small mb-3">
                            Arsip tahun {{ $th }}
                        </p>

                        <!-- BUTTON -->
                        <a href="{{ route('admin.musnah.index', ['tahun' => $th]) }}"
                           class="btn btn-sm shadow-sm mb-2"
                           style="background:#1E3A8A; color:white;">
                            <i class="fas fa-folder-open mr-1"></i> Buka
                        </a>

                        <form action="{{ route('admin.musnah.bulkDelete') }}"
                              method="POST"
                              onsubmit="return confirm('Hapus semua berkas tahun {{ $th }}?')">
                            @csrf
                            @method('DELETE')

                            <input type="hidden" name="tahun" value="{{ $th }}">

                            <button class="btn btn-sm btn-danger shadow-sm w-100">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">
                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                    <br>Belum ada folder berkas
                </div>
            @endforelse
        </div>

    @else

        <!-- HEADER ACTION -->
        <div class="d-sm-flex align-items-center justify-content-between mb-3">
            <a href="{{ route('admin.musnah.index') }}"
               class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>

            <h5 class="font-weight-bold mb-0">
                Berkas Tahun {{ $tahun }}
            </h5>
        </div>

        <!-- CARD TABLE -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header text-white font-weight-bold"
                 style="background:linear-gradient(135deg,#f6d365,#fda085)">
                <i class="fas fa-trash mr-1"></i> Daftar Berkas
            </div>

            <div class="card-body">

                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="thead-light text-center">
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Lemari</th>
                                <th>Jumlah Arsip</th>
                                <th>Tanggal</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            @forelse ($arsips as $arsip)
                                <tr>
                                    <td>
                                        {{ ($arsips->currentPage() - 1) * $arsips->perPage() + $loop->iteration }}
                                    </td>

                                    <td>
                                        {{ $arsip->lemari->nama ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="badge badge-primary px-3">
                                            {{ $arsip->jumlah_arsip ?? 1 }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($arsip->created_at)->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        <div class="btn-group">

                                            <!-- PREVIEW -->
                                            <a href="{{ route('admin.musnah.pdf', $arsip->id) }}"
                                               class="btn btn-sm btn-info shadow-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>


                                            <!-- DELETE -->
                                            <form action="{{ route('admin.musnah.bulkDelete') }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Hapus berkas ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <input type="hidden" name="ids[]" value="{{ $arsip->id }}">

                                                <button class="btn btn-sm btn-danger shadow-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.musnah.download', $arsip->id) }}"
   class="btn btn-success btn-sm">
    <i class="fas fa-download"></i>
</a>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted py-4">
                                        <i class="fas fa-folder-open fa-2x mb-2"></i>
                                        <br>Tidak ada data berkas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="mt-3">
                    {{ $arsips->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>

    @endif

</div>
@endsection
