<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans;
    font-size:11px;
    margin-top:40px;
    margin-bottom:40px;
    margin-left:60px;
    margin-right:60px;
    line-height:1.6;
}

/* ================= KOP SURAT ================= */

.nama-instansi{
    font-size:12px;
    font-weight:bold;
    line-height:1.4;
}

.alamat{
    font-size:9px;
    line-height:1.4;
}

.kop-table{
    width:100%;
    margin-bottom:5px;
}

.logo{
    width:80px;
}

.kop-text{
    text-align:center;
}

.kop-text p{
    margin:2px;
}

.garis{
    border-top:3px solid black;
    border-bottom:1px solid black;
    height:4px;
    margin-top:6px;
    margin-bottom:15px;
}

/* ================= PARAGRAF ================= */

p{
    margin:8px 0;
    text-align:justify;
}

/* ================= TABLE ================= */

table{
    width:100%;
    border-collapse: collapse;
}

th{
    border:1px solid black;
    padding:6px;
    font-size:11px;
    font-weight:bold;
    text-align:center;
}

td{
    border:1px solid black;
    padding:5px;
    font-size:11px;
}

.no-border td{
    border:none;
    padding:2px 4px;
}

.center{
    text-align:center;
}

.right{
    text-align:right;
}

/* ================= SPACING SECTION ================= */

.section{
    margin-top:15px;
}

.signature{
    margin-top:50px;
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

<td>

<p class="nama-instansi">
KEMENTERIAN IMIGRASI DAN PEMASYARAKATAN REPUBLIK INDONESIA<br>
DIREKTORAT JENDERAL IMIGRASI<br>
KANTOR WILAYAH JAWA TIMUR<br>
KANTOR IMIGRASI KELAS I KHUSUS TPI SURABAYA
</p>

<p>
Jl. Raya Juanda Km.3 Sidoarjo, Jawa Timur<br>
Website: surabaya.imigrasi.go.id
</p>

</td>

</tr>

</table>

<div class="garis"></div>


<!-- ================= PARAGRAF PEMBUKA ================= -->

<p>
Kami yang bertanda tangan dibawah ini, pada hari 
<b>{{ \Carbon\Carbon::parse($beritaAcara->tanggal_dibuat)->translatedFormat('l') }}</b>,
tanggal 
<b>{{ \Carbon\Carbon::parse($beritaAcara->tanggal_dibuat)->format('d/m/Y') }}</b>.
</p>


<!-- ================= PIHAK PERTAMA ================= -->

<div class="section">

<table class="no-border">

<tr>
<td width="120">Nama</td>
<td>: {{ $beritaAcara->pengirim->name }}</td>
</tr>

<tr>
<td>Jabatan</td>
<td>: Petugas Pengirim</td>
</tr>

</table>

</div>

<p>Selanjutnya disebut <b>Pihak Pertama</b>.</p>


<!-- ================= PIHAK KEDUA ================= -->

<div class="section">

<table class="no-border">

<tr>
<td width="120">Nama</td>
<td>: {{ auth()->user()->name }}</td>
</tr>

<tr>
<td>Jabatan</td>
<td>: Petugas Arsip</td>
</tr>

</table>

</div>

<p>Selanjutnya disebut <b>Pihak Kedua</b>.</p>


<p>
Pihak Pertama telah menyerahkan arsip kepada Pihak Kedua,
dan Pihak Kedua menyatakan telah menerima arsip dari
Pihak Pertama dengan rincian sebagai berikut:
</p>


<!-- ================= TABEL ARSIP ================= -->

<div class="section">

<table>

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


<p class="section">
Demikian Berita Acara Serah Terima Arsip ini dibuat dengan
sebenar-benarnya dan disepakati oleh kedua belah pihak.
Sejak penandatanganan berita acara ini, maka arsip tersebut
menjadi tanggung jawab Pihak Kedua.
</p>


<!-- ================= TANDA TANGAN ================= -->

<table class="no-border signature">

<tr>

<td class="center">

Yang Menyerahkan<br>
Pihak Pertama

<br><br><br><br>

<b>{{ $beritaAcara->pengirim->name }}</b>

</td>

<td class="center">

Yang Menerima<br>
Pihak Kedua

<br><br><br><br>

<b>{{ auth()->user()->name }}</b>

</td>

</tr>

</table>

</body>
</html>