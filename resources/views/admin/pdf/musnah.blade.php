<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

/* ================= GLOBAL ================= */

body{
    font-family: DejaVu Sans;
    font-size:10px;
    line-height:1.6;
    margin:40px 60px;
}

/* ================= KOP SURAT ================= */

.kop-table{
    width:100%;
}

.logo{
    width:85px;
}

.nama-instansi{
    font-size:10px;
    font-weight:bold;
    line-height:1.3;
    letter-spacing:0.3px;
}

.alamat{
    font-size:9px;
}

.garis{
    border-top:3px solid black;
    border-bottom:1px solid black;
    height:4px;
    margin-top:8px;
    margin-bottom:18px;
}

/* ================= JUDUL ================= */

.judul{
    text-align:center;
    font-weight:bold;
    font-size:11px;
    margin-bottom:15px;
}

/* ================= TABLE ================= */

table{
    width:100%;
    border-collapse:collapse;
}

th{
    border:1px solid black;
    padding:7px;
    font-weight:bold;
    text-align:center;
    background:#f2f2f2;
}

td{
    border:1px solid black;
    padding:6px;
}

.no-border td{
    border:none;
    padding:0;
}

.text-left{
    text-align:left;
}

.center{
    text-align:center;
}

/* ================= TTD ================= */

.footer{
    margin-top:40px;
    width:100%;
}

.ttd{
    width:200px;
    float:right;
    text-align:center;
}

.ttd-space{
    height:60px;
}

</style>
</head>

<body>

<!-- ================= KOP SURAT ================= -->

<table class="kop-table no-border">
<tr>

<td width="90">
    <img src="{{ public_path('img/logo_imigrasi.png') }}" class="logo">
</td>

<td class="center">

<div class="nama-instansi">
KEMENTERIAN IMIGRASI DAN PEMASYARAKATAN REPUBLIK INDONESIA
</div>

<div class="nama-instansi">
DIREKTORAT JENDERAL IMIGRASI
</div>

<div class="nama-instansi">
KANTOR WILAYAH JAWA TIMUR
</div>

<div class="nama-instansi">
KANTOR IMIGRASI KELAS I KHUSUS TPI SURABAYA
</div>

<div class="alamat">
Jl. Raya Juanda Km.3 Sidoarjo, Jawa Timur<br>
Website: surabaya.imigrasi.go.id
</div>

</td>

</tr>
</table>

<div class="garis"></div>

<!-- ================= JUDUL ================= -->

<div class="judul">
DATA ARSIP MUSNAH
</div>

<!-- ================= TABLE ================= -->

<table>
    <tr>
        <th width="5%">No</th>
        <th>Nama</th>
        <th>No Paspor</th>
        <th>Kode Permohonan</th>
        <th>Nama Lemari</th>
        <th>Tanggal</th>
    </tr>

    <tr>
        <td class="center">1</td>
        <td class="text-left">{{ $arsip->nama_lengkap }}</td>
        <td class="center">{{ $arsip->nomor_paspor }}</td>
        <td class="center">{{ $arsip->kode_permohonan }}</td>
        <td class="center">{{ $arsip->lemari->nama ?? '-' }}</td>
        <td class="center">
            {{ \Carbon\Carbon::parse($arsip->created_at)->format('d/m/Y') }}
        </td>
    </tr>
</table>

<!-- ================= FOOTER ================= -->

<div class="footer">
    <div class="ttd">
        <p>Surabaya, {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
        <p>Petugas Arsip</p>

        <div class="ttd-space"></div>

        <p><strong>(___________________)</strong></p>
    </div>
</div>

</body>
</html>
