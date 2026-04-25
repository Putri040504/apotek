@extends('admin.layout.app')

@section('title')
Laporan Penjualan Jenis Obat
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

<div>
<form method="GET" class="d-flex gap-2">

<select name="bulan" class="form-select" style="width:180px">

<option value="">Pilih Bulan</option>

@for($i=1;$i<=12;$i++)
<option value="{{ $i }}" {{ (request('bulan', date('n'))==$i) ? 'selected' : '' }}>
{{ \Carbon\Carbon::create()->month($i)->locale('id')->translatedFormat('F') }}
</option>
@endfor

</select>


<select name="tahun" class="form-select" style="width:150px">

<option value="">Pilih Tahun</option>

@for($t=2026;$t<=2035;$t++)
<option value="{{ $t }}" {{ (request('tahun', date('Y'))==$t) ? 'selected' : '' }}>
{{ $t }}
</option>
@endfor

</select>


<select name="obat" class="form-select" style="width:250px">

<option value="">Pilih Obat</option>

@foreach($obats as $o)

<option value="{{ $o->id }}" {{ request('obat')==$o->id ? 'selected' : '' }}>
{{ $o->nama_obat }}
</option>

@endforeach

</select>

<button class="btn btn-success">
<i class="bi bi-search"></i> Check Data
</button>

</form>
</div>


<div class="d-flex gap-2">

<a href="{{ route('laporan.penjualan.jenis.pdf',[
'bulan'=>request('bulan',date('n')),
'tahun'=>request('tahun',date('Y')),
'obat'=>request('obat')
]) }}"
class="btn btn-outline-danger">

<i class="bi bi-file-earmark-pdf-fill"></i> PDF

</a>

<a href="{{ route('laporan.penjualan.jenis.excel',[
'bulan'=>request('bulan',date('n')),
'tahun'=>request('tahun',date('Y')),
'obat'=>request('obat')
]) }}"
class="btn btn-outline-success">

<i class="bi bi-file-earmark-excel"></i> Excel

</a>

</div>

</div>


@if(!empty($data))

<div class="card shadow-sm border-0">

<div class="card-body">

<table id="tabelPenjualan" class="table table-bordered table-hover table-sm text-center align-middle" style="font-size:14px;">

<thead class="header-hijau">

<tr>
<th width="60">No</th>
<th>No Faktur</th>
<th>Tanggal Faktur</th>
<th>Harga Jual</th>
<th>Jumlah</th>
<th>Total Penjualan</th>
</tr>

</thead>

<tbody>

@foreach($data as $d)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $d->no_transaksi }}</td>

<td>{{ \Carbon\Carbon::parse($d->tanggal_transaksi)->translatedFormat('d F Y') }}</td>

<td class="text-end">
Rp {{ number_format($d->harga,0,',','.') }}
</td>

<td>{{ $d->jumlah }}</td>

<td class="text-end">
Rp {{ number_format($d->total_penjualan,0,',','.') }}
</td>

</tr>

@endforeach

</tbody>

</table>

<div class="alert alert-success mt-3">

Total biaya penjualan adalah sebesar  
<b>Rp {{ number_format($total,0,',','.') }}</b>

</div>

</div>

</div>

@endif


@push('scripts')

<script>

$(document).ready(function(){

$('#tabelPenjualan').DataTable({

pageLength:5,

lengthMenu:[
[5,10,25,50],
[5,10,25,50]
],

language:{
search:"Search:",
lengthMenu:"Tampilkan _MENU_ data",
zeroRecords:"Data tidak ditemukan",
info:"Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
infoEmpty:"Tidak ada data",
infoFiltered:"(difilter dari _MAX_ total data)",
paginate:{
previous:"Sebelumnya",
next:"Berikutnya"
}
}

});

});

</script>

@endpush

@endsection