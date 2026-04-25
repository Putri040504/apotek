@extends('admin.layout.app')

@section('title')
Laporan Pembelian Berdasarkan Jenis Obat
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

<div>

<form method="GET" class="d-flex gap-2 align-items-center">

<select name="bulan" class="form-select" style="width:150px">

<option value="">Pilih Bulan</option>

<option value="1" {{ $bulan == 1 ? 'selected' : '' }}>Januari</option>
<option value="2" {{ $bulan == 2 ? 'selected' : '' }}>Februari</option>
<option value="3" {{ $bulan == 3 ? 'selected' : '' }}>Maret</option>
<option value="4" {{ $bulan == 4 ? 'selected' : '' }}>April</option>
<option value="5" {{ $bulan == 5 ? 'selected' : '' }}>Mei</option>
<option value="6" {{ $bulan == 6 ? 'selected' : '' }}>Juni</option>
<option value="7" {{ $bulan == 7 ? 'selected' : '' }}>Juli</option>
<option value="8" {{ $bulan == 8 ? 'selected' : '' }}>Agustus</option>
<option value="9" {{ $bulan == 9 ? 'selected' : '' }}>September</option>
<option value="10" {{ $bulan == 10 ? 'selected' : '' }}>Oktober</option>
<option value="11" {{ $bulan == 11 ? 'selected' : '' }}>November</option>
<option value="12" {{ $bulan == 12 ? 'selected' : '' }}>Desember</option>

</select>

<select name="tahun" class="form-select" style="width:120px">

@for($i = date('Y'); $i <= date('Y') + 5; $i++)
<option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
{{ $i }}
</option>
@endfor

</select>

<select name="obat" class="form-select" style="width:220px">

<option value="">Pilih Obat</option>

@foreach($obats as $o)

<option value="{{ $o->id }}" {{ $obat == $o->id ? 'selected' : '' }}>
{{ $o->nama_obat }}
</option>

@endforeach

</select>

<button class="btn btn-success">
<i class="bi bi-search"></i> Check Data
</button>

</form>

</div>

@if($bulan)
<div class="d-flex gap-2">

<a href="{{ route('laporan.pembelian.jenis.excel',['bulan'=>$bulan,'tahun'=>$tahun,'obat'=>$obat]) }}" class="btn btn-outline-success">
<i class="bi bi-file-earmark-excel"></i> Excel
</a>

<a href="{{ route('laporan.pembelian.jenis.pdf',['bulan'=>$bulan,'tahun'=>$tahun,'obat'=>$obat]) }}" class="btn btn-outline-danger">
<i class="bi bi-file-earmark-pdf-fill"></i> PDF
</a>

</div>
@endif

</div>


@if($bulan)

<div class="card shadow-sm border-0">

<div class="card-body">

<table id="tabelPembelianJenis" class="table table-bordered table-hover table-sm text-center align-middle" style="font-size:14px;">

<thead class="header-hijau">

<tr>
<th width="60">No</th>
<th>Kode Transaksi</th>
<th>Tanggal</th>
<th>Supplier</th>
<th>Harga Modal</th>
<th>Jumlah</th>
<th>Total Pembelian</th>
</tr>

</thead>

<tbody>

@foreach($data as $d)

<tr>

<td>{{ $loop->iteration }}</td>
<td>{{ $d->kode_transaksi }}</td>
<td>{{ $d->tanggal_transaksi }}</td>
<td>{{ $d->supplier }}</td>
<td>Rp {{ number_format($d->harga_modal,0,',','.') }}</td>
<td>{{ $d->jumlah }}</td>
<td class="text-end">Rp {{ number_format($d->total_pembelian,0,',','.') }}</td>

</tr>

@endforeach

</tbody>

</table>

<div class="alert alert-success mt-3">

Total biaya pembelian adalah sebesar  
<b>Rp {{ number_format($total,0,',','.') }}</b>

</div>

</div>
</div>

@endif


@push('scripts')

<script>

$(document).ready(function(){

$('#tabelPembelianJenis').DataTable({

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