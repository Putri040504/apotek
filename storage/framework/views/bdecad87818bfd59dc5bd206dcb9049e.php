<div class="modal fade" id="modalEdit<?php echo e($o->id); ?>" tabindex="-1">

<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<form action="<?php echo e(route('obat.update',$o->id)); ?>" method="POST">

<?php echo csrf_field(); ?>
<?php echo method_field('PUT'); ?>

<div class="modal-header bg-success text-white">
<h5 class="modal-title" style="font-size:16px;">
<i class="bi bi-pencil-square me-2"></i> Edit Data Obat
</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>


<div class="modal-body" style="font-size:14px;">

<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label">Kode Obat</label>
<input type="text"
name="kode_obat"
value="<?php echo e($o->kode_obat); ?>"
class="form-control bg-light"
readonly
style="cursor:not-allowed">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Nama Obat</label>
<input type="text" name="nama_obat" value="<?php echo e($o->nama_obat); ?>" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Kategori</label>

<select name="kategori_id" class="form-control">

<?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<option value="<?php echo e($k->id); ?>"
<?php echo e($o->kategori_id == $k->id ? 'selected' : ''); ?>>

<?php echo e($k->nama_kategori); ?>


</option>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</select>

</div>

<div class="col-md-6 mb-3">
<label class="form-label">Tanggal Exp</label>
<input type="date" name="tanggal_exp" value="<?php echo e($o->tanggal_exp); ?>" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Stok</label>
<input type="number" name="stok" value="<?php echo e($o->stok); ?>" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Harga Beli</label>
<input type="text" name="harga_beli" value="<?php echo e($o->harga_beli); ?>" class="form-control rupiah">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Harga Jual</label>
<input type="text" name="harga_jual" value="<?php echo e($o->harga_jual); ?>" class="form-control rupiah">
</div>

</div>

</div>

<div class="modal-footer">

<button class="btn btn-success btn-sm">
<i class="bi bi-save"></i> Update
</button>

<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
Batal
</button>

</div>

</form>

</div>
</div>
</div><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/data_obat/modal_edit.blade.php ENDPATH**/ ?>