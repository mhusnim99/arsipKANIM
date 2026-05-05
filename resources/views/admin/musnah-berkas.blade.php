@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Menu Musnah Berkas
        </h1>
    </div>

    <!-- TABLE CARD -->
    <div class="card shadow border-0">
        <div class="card-header text-white font-weight-bold"
             style="background:linear-gradient(135deg,#1E3A8A,#3B82F6)">
            <i class="fas fa-archive mr-1"></i> Data Arsip Siap Musnah
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    
                    <!-- HEADER -->
                    <thead class="thead-light text-center">
                        <tr>
                            <th width="60">No</th>
                            <th>Tahun</th>
                            <th>Jumlah Arsip</th>
                            <th width="250">Aksi</th>
                        </tr>
                    </thead>

                    <!-- BODY -->
                    <tbody class="text-center">

                        @forelse ($years as $index => $th)
                            <tr>
                                <!-- NO -->
                                <td>{{ $index + 1 }}</td>

                                <!-- TAHUN -->
                                <td>
                                    <strong>{{ $th->tahun }}</strong>
                                </td>

                                <!-- JUMLAH -->
                                <td>
                                    <span class="badge badge-primary px-3 py-2">
                                        {{ number_format($th->total) }} arsip
                                    </span>
                                </td>

                                <!-- AKSI -->
                                <td>

                                    @if($th->total > 0)

                                        <!-- DOWNLOAD -->
                                        <div class="d-flex flex-column">

                                            <!-- PART 1 -->
                                            <a href="{{ route('admin.musnah.download.csv', [$th->tahun]) }}"
                                            class="btn btn-success btn-sm mb-2 w-100">
                                                <i class="fas fa-file-csv mr-1"></i>
                                                Download CSV
                                            </a>
                                        </div>
                                        <!-- DELETE -->
                                        <form action="{{ route('admin.musnah.destroy', $th->tahun) }}"
                                              method="POST"
                                              style="display:inline-block"
                                              onsubmit="return confirm('PERMANEN: Hapus {{ number_format($th->total) }} arsip tahun {{ $th->tahun }}?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger btn-sm shadow-sm">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>

                                    @else

                                        <span class="badge badge-secondary">
                                            Kosong
                                        </span>

                                    @endif

                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-muted py-4">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <br>Belum ada arsip untuk dimusnahkan
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection