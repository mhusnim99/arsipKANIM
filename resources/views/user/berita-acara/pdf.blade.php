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
    font-size:10px;
    margin-bottom:3px;
}

.nomor{
    text-align:center;
    font-size:10px;
    margin-bottom:20px;
}

/* ================= PARAGRAF ================= */

p{
    margin:8px 0;
    text-align:justify;
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
}

td{
    border:1px solid black;
    padding:6px;
}

.no-border td{
    border:none;
    padding:2px 4px;
}

/* ================= ALIGN ================= */

.center{
    text-align:center;
}

.right{
    text-align:right;
}

/* ================= SECTION ================= */

.section{
    margin-top:15px;
}

/* ================= TABLE ARSIP ================= */

.table-arsip th{
    background:#f2f2f2;
}

/* ================= TTD ================= */

.signature{
    margin-top:60px;
}

.ttd-nama{
    margin-top:60px;
    font-weight:bold;
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
BERITA ACARA SERAH TERIMA ARSIP
</div>

<div class="nomor">
Nomor : {{ $beritaAcara->nomor ?? '-' }}
</div>


<!-- ================= PARAGRAF PEMBUKA ================= -->

<p>
Kami yang bertanda tangan di bawah ini, pada hari
<b>{{ \Carbon\Carbon::parse($beritaAcara->tanggal_dibuat)->locale('id')->translatedFormat('l') }}</b>,
tanggal
<b>{{ \Carbon\Carbon::parse($beritaAcara->tanggal_dibuat)->locale('id')->translatedFormat('d F Y') }}</b>,
telah melaksanakan serah terima arsip dengan keterangan sebagai berikut:
</p>

<!-- ================= PIHAK PERTAMA ================= -->

<div class="section">

<table class="no-border">

<tr>
<td width="130">Nama</td>
<td>: </td>
</tr>

<tr>
<td>Jabatan</td>
<td>: </td>
</tr>

</table>

</div>

<p>Selanjutnya disebut sebagai <b>Pihak Pertama</b>.</p>


<!-- ================= PIHAK KEDUA ================= -->

<div class="section">

<table class="no-border">

<tr>
<td width="130">Nama</td>
<td>: </td>
</tr>

<tr>
<td>Jabatan</td>
<td>:</td>
</tr>

</table>

</div>

<p>Selanjutnya disebut sebagai <b>Pihak Kedua</b>.</p>


<p>
Pihak Pertama telah menyerahkan arsip kepada Pihak Kedua sebanyak
<b>{{ $beritaAcara->pengirimanBerkas->count() }}</b> berkas, dan Pihak Kedua
menyatakan telah menerima arsip sebanyak
<b>{{ $beritaAcara->pengirimanBerkas->count() }}</b> berkas tersebut dengan rincian sebagai berikut:
</p>

<!-- ================= TABEL ARSIP ================= -->

<div class="section">

<table class="table-arsip">

<thead>

<tr>
<th width="40">No</th>
<th>Kode Permohonan</th>
<th width="120">Tanggal Kirim</th>
<th>Asal Berkas</th>
</tr>

</thead>

<tbody>

@foreach($beritaAcara->pengirimanBerkas as $item)

<tr>

<td class="center">{{ $loop->iteration }}</td>

<td>{{ $item->kode_permohonan }}</td>

<td class="center">
{{ \Carbon\Carbon::parse($item->tanggal_kirim)->format('d/m/Y') }}
</td>

<td>{{ $item->asal_berkas }}</td>

</tr>

@endforeach

</tbody>

</table>

</div>


<!-- ================= PENUTUP ================= -->

<p class="section">
Demikian Berita Acara Serah Terima Arsip ini dibuat dengan sebenar-benarnya
untuk dipergunakan sebagaimana mestinya. Sejak ditandatanganinya berita acara
ini, maka arsip tersebut menjadi tanggung jawab Pihak Kedua.
</p>


<!-- ================= TANDA TANGAN ================= -->

<table class="no-border signature">

<tr>

<td class="center">

Yang Menyerahkan<br>
Pihak Pertama

<div class="ttd-nama">

</div>

</td>


<td class="center">

Yang Menerima<br>
Pihak Kedua

<div class="ttd-nama">

</div>

</td>

</tr>

</table>

</body>
</html>
