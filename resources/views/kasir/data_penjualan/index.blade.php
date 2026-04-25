@extends('kasir.layout.app')

@section('title')
Data Penjualan
@endsection

@if(session('success'))
<script>
document.addEventListener("DOMContentLoaded", function(){
Swal.fire({
icon:'success',
title:'Berhasil',
text:'{{ session('success') }}',
timer:2000,
showConfirmButton:false
});
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener("DOMContentLoaded", function(){
Swal.fire({
icon:'error',
title:'Gagal',
text:'{{ session('error') }}'
});
});
</script>
@endif

@section('content')

<style>

#tabelPenjualan{
font-size:13px;
}

#tabelPenjualan th{
font-size:12px;
padding:6px;
}

#tabelPenjualan td{
padding:5px;
}

/* HEADER TABEL HIJAU */

#tabelPenjualan thead th{
background-color:#198754;
color:white;
text-align:center;
}

/* HOVER ROW */

#tabelPenjualan tbody tr:hover{
background:#e9f7ef;
}

/* FOCUS INPUT JADI HIJAU */

.form-control:focus{
border-color:#198754;
box-shadow:0 0 0 0.2rem rgba(25,135,84,0.25);
}

/* SEARCH DATATABLE */

.dataTables_filter input{
border:1px solid #198754;
}

.dataTables_filter input:focus{
border-color:#198754;
box-shadow:0 0 0 0.2rem rgba(25,135,84,0.25);
}

/* SELECT DATATABLE */

.dataTables_length select{
border:1px solid #198754;
}

.dataTables_length select:focus{
border-color:#198754;
box-shadow:0 0 0 0.2rem rgba(25,135,84,0.25);
}

/* PAGINATION */

.page-item.active .page-link{
background:#198754;
border-color:#198754;
}

.page-link{
color:#198754;
}

.page-link:hover{
color:#146c43;
}

/* TOMBOL PRINT JADI HIJAU */

.btn-outline-primary{
border-color:#198754;
color:#198754;
}

.btn-outline-primary:hover{
background:#198754;
color:white;
}

/* TOMBOL KERANJANG JADI MERAH */

.btn-cart{
background:#dc3545 !important;
border-color:#dc3545 !important;
color:white !important;
}

.btn-cart:hover{
background:#bb2d3b !important;
border-color:#b02a37 !important;
color:white !important;
}

/* PAGINATION DATATABLE HIJAU */

.page-link{
color:#198754 !important;
}

.page-link:hover{
background:#198754 !important;
color:white !important;
border-color:#198754 !important;
}

.page-item.active .page-link{
background:#198754 !important;
border-color:#198754 !important;
color:white !important;
}

/* hilangkan biru focus */

.page-link:focus{
box-shadow:0 0 0 0.2rem rgba(25,135,84,0.25) !important;
}


</style>


<div class="d-flex justify-content-end align-items-center mb-3">

<div>

<button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
<i class="bi bi-plus-circle"></i> Tambah Data
</button>

<button class="btn btn-cart position-relative" data-bs-toggle="modal" data-bs-target="#modalKeranjang">

<i class="bi bi-cart3"></i>

@if($keranjang->count() > 0)
<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
{{ $keranjang->count() }}
</span>
@endif

</button>

</div>

</div>


<div class="card">
<div class="card-body">

<div class="table-responsive">

<table id="tabelPenjualan" class="table table-bordered table-striped text-center align-middle">

<thead>

<tr>
<th width="60">No</th>
<th>Kode Transaksi</th>
<th>Tanggal Jual</th>
<th>Nama Obat</th>
<th>Total Item</th>
<th>Total Transaksi</th>
<th width="100">Aksi</th>
</tr>

</thead>

<tbody>

@php $no=1; @endphp

@foreach($penjualan as $p)

<tr>

<td>{{ $no++ }}</td>

<td>{{ $p->no_transaksi }}</td>

<td>{{ $p->tanggal }}</td>

<td class="text-start">

@foreach($p->detail as $d)

• {{ $d->obat->nama_obat ?? '-' }} <br>

@endforeach

</td>

<td>
{{ $p->detail->sum('jumlah') }} Item
</td>

<td class="text-end">
Rp {{ number_format($p->total,0,',','.') }}
</td>

<td>

<a href="{{ route('penjualan.cetak',$p->id) }}"
class="btn btn-sm btn-outline-success"
target="_blank">

<i class="bi bi-printer"></i>

</a>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>
</div>


@include('kasir.data_penjualan.modal_tambah_penjualan')
@include('kasir.data_penjualan.modal_keranjang')

@endsection



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


$('#obat').on('change', function(){

let selected = $(this).find(':selected');

let harga = selected.data('harga');
let exp = selected.data('exp');

$('#harga').val(formatRupiah(harga));
$('#tanggal_exp').val(exp);

});


$('#jumlah').on('keyup change', function(){

let harga = $('#harga').val().replace(/\D/g,'');
let jumlah = $(this).val();

if(harga && jumlah){

let total = harga * jumlah;

$('#total').val(formatRupiah(total));

}

});


function formatRupiah(angka){

return new Intl.NumberFormat('id-ID',{
style:'currency',
currency:'IDR',
minimumFractionDigits:0
}).format(angka);

}

</script>

@endpush