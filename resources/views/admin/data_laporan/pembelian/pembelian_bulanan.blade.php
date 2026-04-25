@extends('admin.layout.app')

@section('title')
Laporan Pembelian Obat
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

<div>
<form method="GET" class="d-flex gap-2">

<select name="bulan" class="form-select" style="width:200px">

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

<select name="tahun" class="form-select" style="width:150px">

<option value="">Pilih Tahun</option>

@for($t=\Carbon\Carbon::now()->year;$t<=\Carbon\Carbon::now()->year+5;$t++)
<option value="{{ $t }}" {{ $tahun==$t ? 'selected':'' }}>
{{ $t }}
</option>
@endfor

</select>

<button class="btn btn-success">
<i class="bi bi-search"></i> Check Data
</button>

</form>
</div>

@if($bulan && $tahun)
<div class="d-flex gap-2">

<a href="{{ route('laporan.pembelian.excel',['bulan'=>$bulan,'tahun'=>$tahun]) }}" class="btn btn-outline-success">
<i class="bi bi-file-earmark-excel"></i> Excel
</a>

<a href="{{ route('laporan.pembelian.pdf',['bulan'=>$bulan,'tahun'=>$tahun]) }}" class="btn btn-outline-danger">
<i class="bi bi-file-earmark-pdf-fill"></i> PDF
</a>

</div>
@endif

</div>


@if($bulan && $tahun)

<div class="card shadow-sm border-0">

<div class="card-body">

<table id="tabelPembelian" class="table table-bordered table-hover table-sm text-center align-middle" style="font-size:14px;">

<thead class="header-hijau">

<tr>
<th width="60">No</th>
<th>No Transaksi</th>
<th>Tanggal</th>
<th>Supplier</th>
<th>Jumlah Item</th>
<th>Total</th>
</tr>

</thead>

<tbody>

@foreach($data as $d)

<tr>

<td>{{ $loop->iteration }}</td>
<td>{{ $d->kode_transaksi }}</td>
<td>{{ $d->tanggal_transaksi }}</td>
<td>{{ $d->supplier }}</td>
<td>{{ $d->jumlah_item }}</td>
<td class="text-end">Rp {{ number_format($d->total_harga,0,',','.') }}</td>

</tr>

@endforeach

</tbody>

</table>

<div class="alert alert-success mt-3">

Total biaya pembelian bulan ini  
<b>Rp {{ number_format($total,0,',','.') }}</b>

</div>

</div>
</div>

@endif


@push('scripts')

<script>

$(document).ready(function(){

$('#tabelPembelian').DataTable({

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