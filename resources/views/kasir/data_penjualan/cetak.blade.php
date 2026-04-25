<!DOCTYPE html>
<html>

<head>

<title>Struk Penjualan</title>

<style>

body{
font-family: monospace;
font-size:13px;
width:260px;
margin:auto;
}

.center{
text-align:center;
}

.line{
border-top:1px dashed #000;
margin:6px 0;
}

.row{
display:flex;
justify-content:space-between;
}

.bold{
font-weight:bold;
}

.small{
font-size:12px;
}

</style>

</head>

<body onload="window.print()">

@php
$bayar = $penjualan->bayar ?? 0;
$kembali = $bayar - $penjualan->total;
$totalItem = 0;
@endphp


<div class="center">

<div class="bold">APOTEK ZEMA</div>

<div class="small">
Jl. Contoh Alamat Apotek<br>
Telp: 0812xxxxxxx
</div>

</div>

<div class="line"></div>

<div class="small">
No : {{ $penjualan->no_transaksi }} <br>
{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y H:i') }} <br>
Kasir : {{ auth()->user()->name }}
</div>

<div class="line"></div>

<div class="row bold">
<div>OBAT</div>
<div>QTY</div>
<div>SUB</div>
</div>

<div class="line"></div>

@foreach($penjualan->detail as $d)

@php
$totalItem += $d->jumlah;
$namaObat = optional($d->obat)->nama_obat ?? 'Obat tidak ditemukan';
@endphp

<div class="row">

<div>{{ \Illuminate\Support\Str::limit($namaObat,12) }}</div>

<div>{{ $d->jumlah }}</div>

<div>{{ number_format($d->subtotal) }}</div>

</div>

@endforeach



<div class="line"></div>

<div class="row">
<div>Item</div>
<div>{{ $totalItem }}</div>
</div>

<div class="row bold">
<div>TOTAL</div>
<div>{{ number_format($penjualan->total) }}</div>
</div>

<div class="row">
<div>TUNAI</div>
<div>{{ number_format($bayar) }}</div>
</div>

<div class="row">
<div>KEMBALI</div>
<div>{{ number_format($kembali) }}</div>
</div>

<div class="line"></div>

<div class="center small">

TERIMA KASIH<br>
SEMOGA LEKAS SEMBUH

</div>

</body>

</html>