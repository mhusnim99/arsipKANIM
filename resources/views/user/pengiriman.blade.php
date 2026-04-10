@extends('layouts.user')

@section('main-content')
<div class="container-fluid pb-5"> {{-- padding bawah global --}}

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 font-weight-bold" style="color:#1E3A8A;">
            Pengiriman Arsip
        </h1>

        <span class="badge px-4 py-2 shadow-sm" style="background:#1E3A8A; color:white;">
            <i class="fas fa-upload mr-1"></i> Kirim Berkas
        </span>
    </div>

    {{-- NOTIF --}}
    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif

    {{-- FORM INPUT --}}
    <div class="card shadow mb-4 border-0">
        <div class="card-header bg-primary text-white">
            <strong>Input Kode Permohonan</strong>
        </div>

        <div class="card-body py-4">

            <form method="POST" action="{{ route('user.pengiriman.fetch') }}" id="formKode">
                @csrf

                <label class="font-weight-bold mb-2">Kode Permohonan</label>

                <div id="kode-wrapper">
                    <div class="input-group mb-2">
                        <input type="text" name="kode_list[]" class="form-control" placeholder="Masukkan kode permohonan..." required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-primary btn-add">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <small class="text-muted">
                    Tambahkan lebih dari satu kode jika diperlukan.
                </small>

                {{-- Hidden textarea --}}
                <textarea name="kode_permohonan" id="kode_hidden" hidden></textarea>

                <div class="mt-3">
                    <button class="btn btn-primary px-4">
                        <i class="fas fa-search mr-1"></i> Cari Data
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- HASIL --}}
    @if(session('simkim_multiple'))

    <div class="card shadow mt-4 mb-5 border-0"> {{-- mb-5 biar ada jarak bawah --}}
        <div class="card-header bg-success text-white">
            <strong>Hasil Pencarian ({{ count(session('simkim_multiple')) }} data)</strong>
        </div>

        <div class="card-body py-4">

            {{-- ERROR --}}
            @if(session('simkim_error') && count(session('simkim_error')))
                <div class="alert alert-danger">
                    <strong>Data tidak ditemukan:</strong>
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
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>Kode Permohonan</th>
                                <th>Nama</th>
                                <th>No. Paspor</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach(session('simkim_multiple') as $i => $data)
                            @php $p = $data['permohonan']; @endphp
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td><strong>{{ $p['nopermohonan'] }}</strong></td>
                                <td>{{ $p['nama_lengkap'] }}</td>
                                <td>{{ $p['nopaspor'] }}</td>
                                <td>
                                    <span class="badge badge-info px-3 py-2">
                                        {{ $p['alurterakhir'] }}
                                    </span>
                                </td>
                            </tr>

                            <input type="hidden" name="data[]" value='@json($data)'>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ACTION BUTTON --}}
                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-success px-4">
                        <i class="fas fa-paper-plane mr-1"></i>
                        Kirim Semua Berkas
                    </button>
                </div>

            </form>

        </div>
    </div>

    @endif

</div>

{{-- SCRIPT --}}
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const wrapper = document.getElementById('kode-wrapper');

        wrapper.addEventListener('click', function(e){
            if(e.target.closest('.btn-add')){
                const div = document.createElement('div');
                div.classList.add('input-group','mb-2');

                div.innerHTML = `
                    <input type="text" name="kode_list[]" class="form-control" placeholder="Masukkan kode permohonan..." required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-danger btn-remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                wrapper.appendChild(div);
            }

            if(e.target.closest('.btn-remove')){
                e.target.closest('.input-group').remove();
            }
        });

        document.getElementById('formKode').addEventListener('submit', function(){
            let values = [];
            document.querySelectorAll('input[name="kode_list[]"]').forEach(input => {
                if(input.value.trim() !== ''){
                    values.push(input.value.trim());
                }
            });

            document.getElementById('kode_hidden').value = values.join(',');
        });

    });
</script>
@endsection

@endsection
