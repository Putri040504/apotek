@extends('kasir.layout.app')

@section('title')
Riwayat Penjualan
@endsection

@section('content')

<style>

/* SEARCH DATATABLE */
.dataTables_filter input{
border:1px solid #198754 !important;
}

/* PAGINATION */
.page-item.active .page-link{
background-color:#198754 !important;
border-color:#198754 !important;
}

.page-link{
color:#198754 !important;
}

.page-link:hover{
background:#198754 !important;
color:white !important;
}

/* ICON AKSI DEFAULT (PUTIH) */
.btn-detail{
background:white !important;
border:1px solid #198754 !important;
color:#198754 !important;
}

/* HOVER BARU HIJAU */
.btn-detail:hover{
background:#198754 !important;
color:white !important;
}

/* SELECT / INPUT FOCUS (hilangkan biru jadi hijau) */
.form-select:focus,
.form-control:focus{
border-color:#198754 !important;
box-shadow:0 0 0 0.2rem rgba(25,135,84,0.25) !important;
}


/* PAGINATION ANGKA */
.page-link{
color:#198754 !important;
}

/* HOVER PAGINATION */
.page-link:hover{
background:#198754 !important;
color:white !important;
border-color:#198754 !important;
}

/* ACTIVE PAGE */
.page-item.active .page-link{
background:#198754 !important;
border-color:#198754 !important;
color:white !important;
}

/* HEADER TABEL HIJAU TUA */

.table-header-hijau th{
background:#198754 !important;
color:white !important;
text-align:center;
}

table thead th{
text-align:center !important;
vertical-align:middle;
}

/* UKURAN TABEL BIAR PROPORSIONAL */

#tabelRiwayat{
font-size:13px;
}

#tabelRiwayat th{
font-size:12px;
padding:6px;
}

#tabelRiwayat td{
padding:5px;
}

/* HEADER TABEL HIJAU */

#tabelRiwayat thead th{
background:#198754;
color:white;
text-align:center;
}

/* HOVER ROW */

#tabelRiwayat tbody tr:hover{
background:#e9f7ef;
}

</style>


<div class="d-flex justify-content-between align-items-center mb-3">

<div>

<form method="GET" class="d-flex gap-2">

<select name="bulan" class="form-select" style="width:200px">

<option value="">Pilih Bulan</option>

@for($i=1;$i<=12;$i++)
<option value="{{ $i }}" {{ $bulan==$i ? 'selected':'' }}>
{{ \Carbon\Carbon::create()->month($i)->locale('id')->translatedFormat('F') }}
</option>
@endfor

</select>


<select name="tahun" class="form-select" style="width:200px">

<option value="">Pilih Tahun</option>

@for($t=\Carbon\Carbon::now()->year;$t<=\Carbon\Carbon::now()->year+5;$t++)
<option value="{{ $t }}" {{ $tahun==$t ? 'selected':'' }}>
{{ $t }}
</option>
@endfor

</select>


<button class="btn btn-success">
Filter
</button>

</form>

</div>


<div>

<a href="{{ url('/kasir/riwayat/excel?bulan='.$bulan.'&tahun='.$tahun) }}"
class="btn btn-outline-success me-2">

<i class="bi bi-file-earmark-excel-fill"></i> Excel

</a>

<a href="{{ url('/kasir/riwayat/pdf?bulan='.$bulan.'&tahun='.$tahun) }}"
class="btn btn-outline-danger">

<i class="bi bi-file-earmark-pdf-fill"></i> PDF

</a>

</div>

</div>



<div class="card shadow-sm border-0 mt-2">

<div class="card-body">

<table id="tabelRiwayat"
class="table table-bordered table-hover text-center align-middle">

<thead class="table-header-hijau">

<tr>
<th width="60">No</th>
<th>Kode Transaksi</th>
<th>Tanggal</th>
<th>Total Item</th>
<th>Total Transaksi</th>
<th width="120">Aksi</th>
</tr>

</thead>

<tbody>

@foreach($riwayat as $r)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $r->no_transaksi }}</td>

<td>{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d F Y') }}</td>

<td>{{ $r->detail->sum('jumlah') }} Item</td>

<td>Rp {{ number_format($r->total,0,',','.') }}</td>

<td>

<button 
class="btn btn-success btn-sm btn-detail"
data-id="{{ $r->id }}"
data-bs-toggle="modal"
data-bs-target="#modalDetail">

<i class="bi bi-eye"></i>

</button>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>



<!-- MODAL DETAIL -->
<div class="modal fade" id="modalDetail" tabindex="-1">

<div class="modal-dialog modal-lg modal-dialog-centered">

<div class="modal-content">

<div class="modal-header bg-success text-white">

<h5 class="modal-title">
<i class="bi bi-receipt"></i> Detail Transaksi
</h5>

<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body" id="detailPenjualan">

Loading...

</div>

</div>

</div>

</div>



@push('scripts')

<script>

$(document).ready(function(){

$('#tabelRiwayat').DataTable({

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


document.addEventListener("DOMContentLoaded", function(){

const buttons = document.querySelectorAll('.btn-detail');

buttons.forEach(function(btn){

btn.addEventListener('click', function(){

let id = this.getAttribute('data-id');

fetch('/kasir/riwayat/detail/' + id)
.then(response => response.text())
.then(data => {

document.getElementById('detailPenjualan').innerHTML = data;

});

});

});

});

</script>

@endpush

@endsection