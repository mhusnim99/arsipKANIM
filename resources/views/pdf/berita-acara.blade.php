<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 5px; text-align: center; }
        .signature { margin-top: 50px; width: 100%; }
    </style>
</head>
<body>

<div class="header">
    <h3>KEMENTERIAN HUKUM DAN HAM</h3>
    <h4>KANTOR IMIGRASI</h4>
    <div class="title">BERITA ACARA SERAH TERIMA ARSIP</div>
</div>

<p>
Pada hari ini tanggal {{ $ba->tanggal_approve->format('d-m-Y') }},
telah dilakukan serah terima arsip dari:
</p>

<p>
Petugas Layanan: <strong>{{ $ba->petugasLayanan->name }}</strong><br>
Kepada Petugas Arsip: <strong>{{ $ba->petugasArsip->name }}</strong>
</p>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Permohonan</th>
            <th>Asal Berkas</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ba->pengiriman as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->kode_permohonan }}</td>
            <td>{{ $item->asal_berkas }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p>Total Arsip: {{ $ba->jumlah_arsip }} berkas</p>

<div class="signature">
    <table style="border: none;">
        <tr style="border: none;">
            <td style="border: none; text-align:center;">
                Petugas Layanan<br><br><br>
                ( {{ $ba->petugasLayanan->name }} )
            </td>
            <td style="border: none; text-align:center;">
                Petugas Arsip<br><br><br>
                ( {{ $ba->petugasArsip->name }} )
            </td>
        </tr>
    </table>
</div>

</body>
</html>