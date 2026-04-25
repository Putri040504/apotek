@extends('admin.layout.app')

@section('title')
Data Obat
@endsection

@section('content')

<style>

#tabelObat{
font-size:13px;
}

#tabelObat th{
font-size:12px;
padding:6px;
}

#tabelObat td{
padding:5px;
}

</style>

<div class="d-flex justify-content-between align-items-center mb-3">

<div>

<button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
<i class="bi bi-plus-circle"></i> Tambah Data
</button>

</div>

<div>

<a href="{{ route('obat.excel') }}" class="btn btn-outline-success me-2">
<i class="bi bi-file-earmark-excel-fill"></i> Excel
</a>

<a href="{{ route('obat.pdf') }}" class="btn btn-outline-danger">
<i class="bi bi-file-earmark-pdf-fill"></i> PDF
</a>

</div>

</div>


<div class="card">
<div class="card-body">

<table id="tabelObat" class="table table-bordered text-center align-middle">

<thead class="header-hijau">

<tr>

<th width="60" class="text-center align-middle">No</th>
<th class="text-center align-middle">Kode Obat</th>
<th class="text-center align-middle">Nama Obat</th>
<th class="text-center align-middle">Tanggal EXP</th>
<th class="text-center align-middle">Kategori</th>
<th class="text-center align-middle">Stok</th>
<th class="text-center align-middle">Harga Beli</th>
<th class="text-center align-middle">Harga Jual</th>
<th width="120" class="text-center align-middle">Aksi</th>

</tr>

</thead>

<tbody>

@foreach($obat as $o)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $o->kode_obat }}</td>

<td>{{ $o->nama_obat }}</td>

<td>{{ $o->tanggal_exp }}</td>

<td>{{ $o->kategori->nama_kategori }}</td>

<td>{{ $o->stok }}</td>

<td>Rp {{ number_format($o->harga_beli,0,',','.') }}</td>
<td>Rp {{ number_format($o->harga_jual,0,',','.') }}</td>

<td>

<button
class="btn btn-sm btn-outline-success"
data-bs-toggle="modal"
data-bs-target="#modalEdit{{ $o->id }}">

<i class="bi bi-pencil-square"></i>

</button>


<form id="delete-form-{{ $o->id }}" action="{{ route('obat.destroy',$o->id) }}" method="POST" style="display:inline">

@csrf
@method('DELETE')

<button type="button"
class="btn btn-sm btn-outline-danger"
onclick="confirmDelete({{ $o->id }})">

<i class="bi bi-trash"></i>

</button>

</form>

</td>

</tr>

@include('admin.data_obat.modal_edit')

@endforeach

</tbody>

</table>

</div>
</div>

@include('admin.data_obat.modal_tambah')


@push('scripts')

<script>

$(document).ready(function(){

$('#tabelObat').DataTable({

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


@if(session('success'))

Swal.fire({
icon:'success',
title:'Berhasil',
text:'{{ session('success') }}',
timer:2000,
showConfirmButton:false
})

@endif


@if ($errors->any())

Swal.fire({
icon:'error',
title:'Gagal',
text:'{{ $errors->first() }}'
})

@endif


function confirmDelete(id){

Swal.fire({
title: 'Yakin hapus data?',
text: "Data tidak bisa dikembalikan!",
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#d33',
cancelButtonColor: '#6c757d',
confirmButtonText: 'Ya, hapus!',
cancelButtonText: 'Batal'
}).then((result) => {

if (result.isConfirmed) {
document.getElementById('delete-form-'+id).submit();
}

})

}

</script>

<script>

document.querySelectorAll('.rupiah').forEach(function(el){

el.addEventListener('keyup', function(){

let angka = this.value.replace(/[^,\d]/g,'').toString();
let split = angka.split(',');
let sisa = split[0].length % 3;
let rupiah = split[0].substr(0,sisa);
let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

if(ribuan){
separator = sisa ? '.' : '';
rupiah += separator + ribuan.join('.');
}

this.value = rupiah;

});

});

</script>

@endpush

@endsection