

<?php $__env->startSection('title'); ?>
Data Obat
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

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

<a href="<?php echo e(route('obat.excel')); ?>" class="btn btn-outline-success me-2">
<i class="bi bi-file-earmark-excel-fill"></i> Excel
</a>

<a href="<?php echo e(route('obat.pdf')); ?>" class="btn btn-outline-danger">
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

<?php $__currentLoopData = $obat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr>

<td><?php echo e($loop->iteration); ?></td>

<td><?php echo e($o->kode_obat); ?></td>

<td><?php echo e($o->nama_obat); ?></td>

<td><?php echo e($o->tanggal_exp); ?></td>

<td><?php echo e($o->kategori->nama_kategori); ?></td>

<td><?php echo e($o->stok); ?></td>

<td>Rp <?php echo e(number_format($o->harga_beli,0,',','.')); ?></td>
<td>Rp <?php echo e(number_format($o->harga_jual,0,',','.')); ?></td>

<td>

<button
class="btn btn-sm btn-outline-success"
data-bs-toggle="modal"
data-bs-target="#modalEdit<?php echo e($o->id); ?>">

<i class="bi bi-pencil-square"></i>

</button>


<form id="delete-form-<?php echo e($o->id); ?>" action="<?php echo e(route('obat.destroy',$o->id)); ?>" method="POST" style="display:inline">

<?php echo csrf_field(); ?>
<?php echo method_field('DELETE'); ?>

<button type="button"
class="btn btn-sm btn-outline-danger"
onclick="confirmDelete(<?php echo e($o->id); ?>)">

<i class="bi bi-trash"></i>

</button>

</form>

</td>

</tr>

<?php echo $__env->make('admin.data_obat.modal_edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>

</div>
</div>

<?php echo $__env->make('admin.data_obat.modal_tambah', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php $__env->startPush('scripts'); ?>

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


<?php if(session('success')): ?>

Swal.fire({
icon:'success',
title:'Berhasil',
text:'<?php echo e(session('success')); ?>',
timer:2000,
showConfirmButton:false
})

<?php endif; ?>


<?php if($errors->any()): ?>

Swal.fire({
icon:'error',
title:'Gagal',
text:'<?php echo e($errors->first()); ?>'
})

<?php endif; ?>


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

<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/data_obat/index.blade.php ENDPATH**/ ?>