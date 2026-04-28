<div class="modal fade" id="modalTambah" tabindex="-1">

<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<div class="modal-header bg-success text-white">

<h5 class="modal-title" style="font-size:16px;">
<i class="bi bi-cart-plus"></i> Tambah Data Pembelian
</h5>

<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

</div>

<form id="formTambah" action="<?php echo e(route('pembelian.store')); ?>" method="POST">
<?php echo csrf_field(); ?>

<div class="modal-body" style="font-size:13px;">

<div class="row g-2">

<!-- SUPPLIER -->
<div class="col-md-6">
<label class="form-label">Nama Supplier</label>

<select name="supplier_id" class="form-control form-control-sm select2">
<option value="">-- Pilih Supplier --</option>

<?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<option value="<?php echo e($s->id); ?>">
<?php echo e($s->nama_supplier); ?>

</option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</select>
</div>

<!-- KODE OBAT -->
<div class="col-md-6">
<label class="form-label">Kode Obat</label>
<input type="text" id="kode_obat" name="kode_obat" class="form-control form-control-sm auto-field" readonly>
</div>

<!-- NAMA OBAT -->
<div class="col-md-6">

<label class="form-label">Nama Obat</label>

<select name="obat_id" id="obat" class="form-control form-control-sm select2">

<option value="">-- Pilih Obat --</option>

<?php $__currentLoopData = $obat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<option 
value="<?php echo e($o->id); ?>"
data-kode="<?php echo e($o->kode_obat); ?>"
data-harga="<?php echo e($o->harga_beli); ?>">

<?php echo e($o->nama_obat); ?>


</option>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</select>

</div>

<!-- EXP -->
<div class="col-md-6">
<label class="form-label">Tanggal EXP</label>
<input type="date" id="exp" name="exp" class="form-control form-control-sm">
</div>

<!-- HARGA -->
<div class="col-md-6">
<label class="form-label">Harga</label>
<input type="number" id="harga" name="harga" class="form-control form-control-sm">
</div>

<!-- JUMLAH -->
<div class="col-md-6">
<label class="form-label">Jumlah Barang</label>
<input type="number" id="jumlah" name="qty" class="form-control form-control-sm">
</div>

<!-- TOTAL -->
<div class="col-md-6">
<label class="form-label">Total Biaya</label>
<input type="text" id="total" name="total" class="form-control form-control-sm auto-field" readonly>
</div>

</div>
</div>

<div class="modal-footer">

<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
<i class="bi bi-x-circle"></i> Batal
</button>

<button type="submit" class="btn btn-success btn-sm">
<i class="bi bi-save"></i> Simpan
</button>

</div>

</form>

</div>
</div>
</div><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/data_pembelian/modal_tambah.blade.php ENDPATH**/ ?>