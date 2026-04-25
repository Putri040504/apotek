@extends('admin.layout.app')

@section('title')
Data Kategori
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

<div>
<button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
<i class="bi bi-plus-circle"></i> Tambah Data
</button>
</div>

<div>
<a href="{{ route('kategori.excel') }}" class="btn btn-outline-success me-2">
<i class="bi bi-file-earmark-excel-fill"></i> Excel
</a>

<a href="{{ route('kategori.pdf') }}" class="btn btn-outline-danger">
<i class="bi bi-file-earmark-pdf-fill"></i> PDF
</a>
</div>

</div>


<div class="card shadow-sm border-0">
<div class="card-body">

<table id="tabelKategori" class="table table-bordered table-hover table-sm text-center align-middle" style="font-size:14px;">

<thead class="header-hijau">

<tr>
<th width="60">No</th>
<th>Kode Kategori</th>
<th>Nama Kategori</th>
<th width="120">Aksi</th>
</tr>

</thead>

<tbody>

@foreach($kategori as $k)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $k->kode_kategori }}</td>

<td>{{ $k->nama_kategori }}</td>

<td>

<button
class="btn btn-sm btn-outline-success"
data-bs-toggle="modal"
data-bs-target="#modalEdit{{ $k->id }}"
title="Edit">

<i class="bi bi-pencil-square"></i>

</button>

<form id="delete-form-{{ $k->id }}" 
action="{{ route('kategori.destroy',$k->id) }}" 
method="POST" 
style="display:inline">

@csrf
@method('DELETE')

<button type="button"
class="btn btn-sm btn-outline-danger"
onclick="confirmDelete({{ $k->id }})">

<i class="bi bi-trash"></i>

</button>

</form>

</td>

</tr>

@include('admin.data_kategori.modal_edit')

@endforeach

</tbody>

</table>

</div>
</div>

@include('admin.data_kategori.modal_tambah')


@push('scripts')

<script>

$(document).ready(function(){

$('#tabelKategori').DataTable({

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

@endpush

@endsection