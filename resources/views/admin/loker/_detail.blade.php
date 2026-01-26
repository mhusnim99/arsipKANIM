<div class="modal-header">
    <h5 class="modal-title">
        Detail Loker {{ $loker->display }}
    </h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>

<div class="modal-body">

    {{-- INFO LOKER --}}
    <div class="mb-3">
        <table class="table table-sm table-bordered">
            <tr>
                <th width="35%">Lemari</th>
                <td>{{ $loker->lemari->nama_lemari }}</td>
            </tr>
            <tr>
                <th>Kode Loker</th>
                <td>{{ $loker->display }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge badge-{{ $loker->status_badge }}">
                        {{ $loker->status_text }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Kapasitas</th>
                <td>{{ $loker->kapasitas }}</td>
            </tr>
            <tr>
                <th>Terisi</th>
                <td>{{ $loker->arsips->count() }}</td>
            </tr>
        </table>
    </div>

    {{-- DAFTAR ARSIP --}}
    <h6 class="font-weight-bold mb-2">Daftar Arsip</h6>

    @if ($loker->arsips->isEmpty())
        <div class="alert alert-info">
            Loker ini belum memiliki arsip.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>Nomor Arsip</th>
                        <th>Kode Permohonan</th>
                        <th>Asal Berkas</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loker->arsips as $index => $arsip)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $arsip->nomor_arsip }}</strong></td>
                            <td>{{ $arsip->kode_permohonan }}</td>
                            <td>{{ $arsip->asal_berkas }}</td>
                            <td>{{ $arsip->tanggal_masuk }}</td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ ucfirst($arsip->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        Tutup
    </button>
</div>
