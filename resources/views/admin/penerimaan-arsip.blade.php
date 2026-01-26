@extends('layouts.admin')

@section('main-content')
    <div class="container-fluid">

        <h1 class="h3 mb-4 text-gray-800">
            <i class="fas fa-inbox mr-2"></i>Penerimaan Arsip
        </h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card shadow">
            <div class="card-header bg-warning text-white">
                <strong>Pengiriman Menunggu</strong>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Asal</th>
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengirimanBerkas as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->kode_permohonan }}</td>
                                <td>{{ $item->tanggal_kirim->format('d/m/Y') }}</td>
                                <td>{{ $item->asal_berkas }}</td>
                                <td>{{ optional($item->petugasPengirim)->name ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-success btn-sm btn-terima" data-id="{{ $item->id }}"
                                        data-kode="{{ $item->kode_permohonan }}">
                                        Terima
                                    </button>

                                    <button class="btn btn-danger btn-sm btn-tolak" data-id="{{ $item->id }}"
                                        data-kode="{{ $item->kode_permohonan }}">
                                        Tolak
                                    </button>

                                    <button class="btn btn-info btn-sm btn-detail" data-id="{{ $item->id }}">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $pengirimanBerkas->links() }}
            </div>
        </div>

        {{-- ================= MODAL TERIMA ================= --}}
        <div class="modal fade" id="modalTerima">
            <div class="modal-dialog modal-lg">
                <form method="POST" id="formTerima" class="modal-content">
                    @csrf

                    <div class="modal-header bg-success text-white">
                        <h5>Terima Arsip</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-info">
                            Kode Permohonan: <strong id="kodeTerima"></strong>
                        </div>

                        <div class="form-group">
                            <label>Lemari</label>
                            <select id="lemari_id" name="lemari_id" class="form-control" required>
                                <option value="">-- Pilih Lemari --</option>
                                @foreach ($lemaris as $lemari)
                                    <option value="{{ $lemari->id }}">
                                        {{ $lemari->kode_lemari }} - {{ $lemari->nama_lemari }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Loker</label>
                            <select id="loker_id" name="loker_id" class="form-control" required>
                                <option value="">-- Pilih Loker --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Nomor Arsip</label>
                            <input type="text" id="nomor_arsip" name="nomor_arsip" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL TOLAK ================= --}}
        <div class="modal fade" id="modalTolak">
            <div class="modal-dialog">
                <form method="POST" id="formTolak" class="modal-content">
                    @csrf

                    <div class="modal-header bg-danger text-white">
                        <h5>Tolak Arsip</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <p>Menolak arsip: <strong id="kodeTolak"></strong></p>
                        <textarea name="alasan_penolakan" class="form-control" required></textarea>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button class="btn btn-danger">Tolak</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL DETAIL ================= --}}
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

    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.btn-terima', function() {
            const id = $(this).data('id');
            const kode = $(this).data('kode');
            $('#kodeTerima').text(kode);
            $('#formTerima').attr('action', `/admin/penerimaan-arsip/${id}/terima`);
            $('#modalTerima').modal('show');
        });

        $(document).on('click', '.btn-tolak', function() {
            const id = $(this).data('id');
            const kode = $(this).data('kode');
            $('#kodeTolak').text(kode);
            $('#formTolak').attr('action', `/admin/penerimaan-arsip/${id}/tolak`);
            $('#modalTolak').modal('show');
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

        $(document).on('change', '#lemari_id', function() {
            const lemariId = $(this).val();
            const lokerSelect = $('#loker_id');

            console.log('LEMARI DIPILIH:', lemariId);

            lokerSelect.html('<option value="">Loading...</option>');

            if (!lemariId) {
                lokerSelect.html('<option value="">-- Pilih Loker --</option>');
                return;
            }

            $.get(`/admin/penerimaan-arsip/lemari/${lemariId}/lokers`, function(data) {
                console.log('LOKER:', data);

                let html = '<option value="">-- Pilih Loker --</option>';

                if (data.length === 0) {
                    html = '<option value="">Tidak ada loker tersedia</option>';
                }

                data.forEach(loker => {
                    html += `<option value="${loker.id}">${loker.kode_loker}</option>`;
                });

                lokerSelect.html(html);
            });
        });
    </script>
@endsection
