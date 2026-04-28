<div class="modal fade" id="modalEdit<?php echo e($k->id); ?>">

<div class="modal-dialog modal-lg modal-dialog-centered">

<div class="modal-content">

<form action="<?php echo e(route('kategori.update',$k->id)); ?>" method="POST" autocomplete="off">

<?php echo csrf_field(); ?>
<?php echo method_field('PUT'); ?>

<div class="modal-header bg-success text-white">

<h5 class="modal-title" style="font-size:16px;">
<i class="bi bi-pencil-square me-2"></i> Edit Data Kategori
</h5>

<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body" style="font-size:14px;">

<div class="mb-3">
<label class="form-label">Kode Kategori</label>

<input 
type="text" 
class="form-control bg-light"
value="<?php echo e($k->kode_kategori); ?>"
disabled
style="cursor:not-allowed;">

</div>

<div class="mb-3">
<label class="form-label">Nama Kategori</label>

<input 
type="text"
name="nama_kategori"
value="<?php echo e($k->nama_kategori); ?>"
class="form-control"
autocomplete="off"
spellcheck="false"
autocorrect="off"
autocapitalize="off"
required>

</div>

</div>

<div class="modal-footer">

<button type="submit" class="btn btn-success btn-sm">
<i class="bi bi-save"></i> Update
</button>

<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
Batal
</button>

</div>

</form>

</div>
</div>

</div><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/data_kategori/modal_edit.blade.php ENDPATH**/ ?>