<div class="modal fade" id="modalTambah" tabindex="-1">

<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<form action="<?php echo e(route('obat.store')); ?>" method="POST">

<?php echo csrf_field(); ?>

<div class="modal-header bg-success text-white">
<h5 class="modal-title" style="font-size:16px;">
<i class="bi bi-capsule me-2"></i> Tambah Data Obat
</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body" style="font-size:14px;">

<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label">Kode Obat</label>
<input type="text" name="kode_obat"
value="<?php echo e('OB'.str_pad($obat->count()+1,3,'0',STR_PAD_LEFT)); ?>"
class="form-control bg-light"
readonly
style="cursor:not-allowed">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Nama Obat</label>
<input type="text" name="nama_obat" value="<?php echo e(old('nama_obat')); ?>" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Kategori</label>

<select name="kategori_id" class="form-control">

<option value="">-- Pilih Kategori --</option>

<?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<option value="<?php echo e($k->id); ?>" 
<?php echo e(old('kategori_id') == $k->id ? 'selected' : ''); ?>>

<?php echo e($k->nama_kategori); ?>


</option>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</select>

</div>

<div class="col-md-6 mb-3">
<label class="form-label">Tanggal Exp</label>
<input type="date" name="tanggal_exp" value="<?php echo e(old('tanggal_exp')); ?>" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Stok</label>
<input type="number" name="stok" value="<?php echo e(old('stok')); ?>" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Harga Beli</label>
<input type="text" name="harga_beli" value="<?php echo e(old('harga_beli')); ?>" class="form-control rupiah">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Harga Jual</label>
<input type="text" name="harga_jual" value="<?php echo e(old('harga_jual')); ?>" class="form-control rupiah">
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
</div><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/data_obat/modal_tambah.blade.php ENDPATH**/ ?>