@extends('layouts.user')

@section('main-content')
<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Pengiriman Arsip
        </h1>

        <span class="badge px-4 py-2 shadow-sm" style="background:#1E3A8A; color:white;">
            <i class="fas fa-upload mr-1"></i> Kirim Berkas
        </span>
    </div>
    <div class="container-fluid">
    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- ================= FORM CARI SIMKIM ================= --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Cari Data Permohonan (SIMKIM)</strong>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('user.pengiriman.fetch') }}">
                @csrf

                <div class="form-group">
                    <label>Kode Permohonan</label>
                    <textarea name="kode_permohonan"
                        class="form-control"
                        rows="4"
                        placeholder="Masukkan beberapa kode (pisahkan enter/koma)"
                        required></textarea>
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-search mr-1"></i> Cari Data
                </button>
            </form>
        </div>
    </div>

    {{-- ================= HASIL DATA SIMKIM ================= --}}
    @if(session('simkim_multiple'))

        <div class="card shadow mt-4">
            <div class="card-header bg-success text-white">
                <strong>Hasil Pencarian Multiple</strong>
            </div>

            <div class="card-body">

                {{-- ERROR --}}
                @if(session('simkim_error') && count(session('simkim_error')))
                    <div class="alert alert-danger">
                        <strong>Gagal ditemukan:</strong>
                        {{ implode(', ', session('simkim_error')) }}
                    </div>
                @endif

                {{-- DUPLIKAT --}}
                @if(session('simkim_duplicate') && count(session('simkim_duplicate')))
                    <div class="alert alert-warning">
                        <strong>Sudah pernah dikirim:</strong>
                        {{ implode(', ', session('simkim_duplicate')) }}
                    </div>
                @endif

                <form method="POST" action="{{ route('user.pengiriman.store.multiple') }}">
                    @csrf

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>No Paspor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach(session('simkim_multiple') as $i => $data)
                                    @php $p = $data['permohonan']; @endphp
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $p['nopermohonan'] }}</td>
                                        <td>{{ $p['nama_lengkap'] }}</td>
                                        <td>{{ $p['nopaspor'] }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $p['alurterakhir'] }}
                                            </span>
                                        </td>
                                    </tr>

                                    {{-- Hidden input untuk dikirim --}}
                                    <input type="hidden" name="data[]"
                                        value='@json($data)'>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <button class="btn btn-success">
                        <i class="fas fa-paper-plane mr-1"></i>
                        Kirim Semua Berkas
                    </button>
                </form>

            </div>
        </div>

    @endif
        {{-- <h4>Data Hasil Sinkronisasi Otomatis</h4> --}}

    @if(isset($syncData) && $syncData->count())
        <div class="card shadow mt-4">
            <div class="card-header bg-secondary text-white">
                <strong>Data Hasil Sinkronisasi Otomatis</strong>
            </div>

            <div class="card-body">
                @if($syncData->count())
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Kode Permohonan</th>
                                    <th>Status</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($syncData as $item)
                                    <tr>
                                        <td>{{ $item->kode_permohonan }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $item->status_proses }}
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST"
                                                action="{{ route('user.pengiriman.sync', $item->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-success">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Tidak ada data sinkronisasi tersedia.</p>
                @endif
            </div>
        </div>
    @endif
</div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal_kirim').max = today;
    });
</script>
@endsection

@endsection
