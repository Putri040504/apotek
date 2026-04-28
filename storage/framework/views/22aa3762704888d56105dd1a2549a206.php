

<?php $__env->startSection('title'); ?>
Data Kategori
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">

<div>
<button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
<i class="bi bi-plus-circle"></i> Tambah Data
</button>
</div>

<div>
<a href="<?php echo e(route('kategori.excel')); ?>" class="btn btn-outline-success me-2">
<i class="bi bi-file-earmark-excel-fill"></i> Excel
</a>

<a href="<?php echo e(route('kategori.pdf')); ?>" class="btn btn-outline-danger">
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

<?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr>

<td><?php echo e($loop->iteration); ?></td>

<td><?php echo e($k->kode_kategori); ?></td>

<td><?php echo e($k->nama_kategori); ?></td>

<td>

<button
class="btn btn-sm btn-outline-success"
data-bs-toggle="modal"
data-bs-target="#modalEdit<?php echo e($k->id); ?>"
title="Edit">

<i class="bi bi-pencil-square"></i>

</button>

<form id="delete-form-<?php echo e($k->id); ?>" 
action="<?php echo e(route('kategori.destroy',$k->id)); ?>" 
method="POST" 
style="display:inline">

<?php echo csrf_field(); ?>
<?php echo method_field('DELETE'); ?>

<button type="button"
class="btn btn-sm btn-outline-danger"
onclick="confirmDelete(<?php echo e($k->id); ?>)">

<i class="bi bi-trash"></i>

</button>

</form>

</td>

</tr>

<?php echo $__env->make('admin.data_kategori.modal_edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>

</div>
</div>

<?php echo $__env->make('admin.data_kategori.modal_tambah', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php $__env->startPush('scripts'); ?>

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
<?php echo $__env->make('admin.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/data_kategori/index.blade.php ENDPATH**/ ?>