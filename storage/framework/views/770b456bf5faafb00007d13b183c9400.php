

<?php $__env->startSection('title'); ?>
Data Supplier
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<style>

#tabelSupplier{
font-size:13px;
}

#tabelSupplier th{
font-size:12px;
padding:6px;
}

#tabelSupplier td{
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

<a href="<?php echo e(route('supplier.excel')); ?>" class="btn btn-outline-success me-2">
<i class="bi bi-file-earmark-excel-fill"></i> Excel
</a>

<a href="<?php echo e(route('supplier.pdf')); ?>" class="btn btn-outline-danger">
<i class="bi bi-file-earmark-pdf-fill"></i> PDF
</a>

</div>

</div>


<div class="card">
<div class="card-body">

<table id="tabelSupplier" class="table table-bordered align-middle w-100">

<thead class="header-hijau text-start align-top">
<tr>
<th width="60">No</th>
<th>Kode Supplier</th>
<th>Nama Supplier</th>
<th>Nama Obat</th>
<th>Alamat</th>
<th>Email</th>
<th>No Telp</th>
<th width="120">Aksi</th>
</tr>
</thead>

<tbody>

<?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr>
<td><?php echo e($loop->iteration); ?></td>
<td><?php echo e($s->kode_supplier); ?></td>
<td><?php echo e($s->nama_supplier); ?></td>

<td>
<?php $__empty_1 = true; $__currentLoopData = $s->obat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<?php echo e($o->nama_obat); ?> <br>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
-
<?php endif; ?>
</td>

<td><?php echo e($s->alamat); ?></td>
<td><?php echo e($s->email); ?></td>
<td><?php echo e($s->no_telp); ?></td>

<td>
<button class="btn btn-sm btn-outline-success"
data-bs-toggle="modal"
data-bs-target="#modalEdit<?php echo e($s->id); ?>">
<i class="bi bi-pencil-square"></i>
</button>

<form id="delete-form-<?php echo e($s->id); ?>" action="<?php echo e(route('supplier.destroy',$s->id)); ?>" method="POST" style="display:inline">
<?php echo csrf_field(); ?>
<?php echo method_field('DELETE'); ?>

<button type="button"
class="btn btn-sm btn-outline-danger"
onclick="confirmDelete(<?php echo e($s->id); ?>)">
<i class="bi bi-trash"></i>
</button>
</form>
</td>

</tr>

<?php echo $__env->make('admin.data_supplier.modal_edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>
</table>

</div>
</div>

<?php echo $__env->make('admin.data_supplier.modal_tambah', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startPush('scripts'); ?>

<script>

$(document).ready(function(){

$('#tabelSupplier').DataTable({

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

<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/data_supplier/index.blade.php ENDPATH**/ ?>