<div class="modal fade" id="modalEdit<?php echo e($s->id); ?>" tabindex="-1">

<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<form action="<?php echo e(route('supplier.update',$s->id)); ?>" method="POST">

<?php echo csrf_field(); ?>
<?php echo method_field('PUT'); ?>

<div class="modal-header bg-success text-white">
<h5 class="modal-title" style="font-size:16px;">
<i class="bi bi-pencil-square me-2"></i> Edit Supplier
</h5>

<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body" style="font-size:14px;">

<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label">Kode Supplier</label>
<input type="text"
name="kode_supplier"
value="<?php echo e($s->kode_supplier); ?>"
class="form-control bg-light"
readonly>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Nama Supplier</label>
<input type="text" name="nama_supplier" value="<?php echo e($s->nama_supplier); ?>" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Nama Obat</label>

<select name="obat_id" class="form-control">

<?php $__currentLoopData = $obat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<option value="<?php echo e($o->id); ?>"
<?php echo e($s->obat_id == $o->id ? 'selected' : ''); ?>>

<?php echo e($o->nama_obat); ?>


</option>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</select>

</div>

<div class="col-md-6 mb-3">
<label class="form-label">No Telp</label>
<input type="text" name="no_telp" value="<?php echo e($s->no_telp); ?>" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" value="<?php echo e($s->email); ?>" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Alamat</label>
<input type="text" name="alamat" value="<?php echo e($s->alamat); ?>" class="form-control">
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
</div><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/data_supplier/modal_edit.blade.php ENDPATH**/ ?>