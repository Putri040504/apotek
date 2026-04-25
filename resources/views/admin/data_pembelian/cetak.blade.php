<!DOCTYPE html>
<html>
<head>
<title>Cetak Pembelian</title>

<style>

body{
font-family: "Times New Roman", serif;
font-size:14px;
margin:40px;
color:#000;
}

.header{
width:100%;
border-bottom:2px solid black;
padding-bottom:10px;
margin-bottom:20px;
}

.logo{
width:80px;
}

.judul{
text-align:center;
}

.judul h2{
margin:0;
font-size:24px;
}

.judul p{
margin:2px;
font-size:13px;
}

table{
width:100%;
border-collapse:collapse;
}

.info td{
padding:3px 5px;
}

.table-data{
margin-top:15px;
}

.table-data th,
.table-data td{
border:1px solid black;
padding:6px;
}

.table-data th{
background:#f2f2f2;
}

.ttd{
margin-top:60px;
width:100%;
}

.ttd td{
text-align:center;
}

</style>

</head>

<body onload="window.print()">

<!-- HEADER -->

<table class="header">

<tr>

<td width="90">
<img src="{{ asset('logo/apotek zema.png') }}" class="logo">
</td>

<td class="judul">

<h2>APOTEK ZEMA</h2>
<p>Jl. Contoh Alamat Apotek Zema</p>
<p>Kota Anda, Indonesia</p>

</td>

<td width="90"></td>

</tr>

</table>

<h3 style="text-align:center;margin-bottom:15px;">
NOTA PEMBELIAN OBAT
</h3>


<!-- INFO TRANSAKSI -->

<table class="info">

<tr>

<td width="150">No Transaksi</td>
<td width="10">:</td>
<td>{{ $pembelian->kode_transaksi }}</td>

<td width="150">Nama Supplier</td>
<td width="10">:</td>
<td>{{ $pembelian->supplier->nama_supplier ?? '-' }}</td>

</tr>

<tr>

<td>Tanggal Transaksi</td>
<td>:</td>
<td>{{ $pembelian->tanggal }}</td>

<td>Alamat Supplier</td>
<td>:</td>
<td>{{ $pembelian->supplier->alamat ?? '-' }}</td>

</tr>

<tr>

<td></td>
<td></td>
<td></td>

<td>No Telp</td>
<td>:</td>
<td>{{ $pembelian->supplier->no_telp ?? '-' }}<td>

</tr>

</table>


<!-- TABEL OBAT -->

<table class="table-data">

<thead>

<tr>

<th width="50">No</th>
<th>Kode Obat</th>
<th>Nama Obat</th>
<th>Kategori</th>
<th width="100">Jumlah</th>
<th width="150">Harga Beli</th>

</tr>

</thead>

<tbody>

@php $no = 1; @endphp

@foreach($pembelian->detail as $d)

<tr>

<td align="center">{{ $no++ }}</td>

<td>{{ $d->obat->kode_obat ?? '-' }}</td>

<td>{{ $d->obat->nama_obat ?? '-' }}</td>

<td>{{ $d->obat->kategori->nama_kategori ?? '-' }}</td>

<td align="center">{{ $d->jumlah }}</td>

<td align="right">Rp {{ number_format($d->harga,0,',','.') }}</td>

</tr>

@endforeach

</tbody>

</table>


<!-- TANDA TANGAN -->

<table class="ttd">

<tr>

<td width="70%"></td>

<td>

Padang, {{ date('d-m-Y') }}

<br><br>

Pimpinan

<br><br><br>

(____________________)

</td>

</tr>

</table>


</body>
</html>