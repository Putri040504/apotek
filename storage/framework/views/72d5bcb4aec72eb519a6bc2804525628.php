<div class="modal fade" id="modalTambah" tabindex="-1">

<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<form action="<?php echo e(route('supplier.store')); ?>" method="POST">

<?php echo csrf_field(); ?>

<div class="modal-header bg-success text-white">
<h5 class="modal-title" style="font-size:16px;">
<i class="bi bi-plus-circle me-2"></i> Tambah Supplier
</h5>

<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body" style="font-size:14px;">

<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label">Kode Supplier</label>
<input type="text" name="kode_supplier" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Nama Supplier</label>
<input type="text" name="nama_supplier" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Nama Obat</label>

<select name="obat_id" class="form-control">

<?php $__currentLoopData = $obat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<option value="<?php echo e($o->id); ?>">
<?php echo e($o->nama_obat); ?>

</option>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</select>

</div>

<div class="col-md-6 mb-3">
<label class="form-label">No Telp</label>
<input type="text" name="no_telp" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Alamat</label>
<input type="text" name="alamat" class="form-control">
</div>

</div>

</div>

<div class="modal-footer">

<button class="btn btn-success btn-sm">
<i class="bi bi-save"></i> Simpan
</button>

<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
Batal
</button>

</div>

</form>

</div>
</div>
</div><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/data_supplier/modal_tambah.blade.php ENDPATH**/ ?>