<style>

#tabelKeranjang{
font-size:13px;
}

#tabelKeranjang th{
font-size:12px;
padding:6px;
}

#tabelKeranjang td{
padding:5px;
}

</style>


<div class="modal fade" id="modalKeranjang">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<!-- HEADER -->
<div class="modal-header bg-success text-white">

<h5 class="modal-title" style="font-size:16px;">
<i class="bi bi-cart3"></i> Keranjang Pembelian
</h5>

<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

</div>


<form id="formCheckout" action="<?php echo e(route('pembelian.checkout')); ?>" method="POST">
<?php echo csrf_field(); ?>

<div class="modal-body" style="font-size:13px;">

<div class="table-responsive">

<table id="tabelKeranjang" class="table table-bordered text-center align-middle">

<thead class="header-hijau text-center align-top">
<tr>
<th width="50">No</th>
<th>Kode Keranjang</th>
<th>Kode Supplier</th>
<th>Kode Obat</th>
<th>Tanggal</th>
<th>Jumlah</th>
<th>Total</th>
<th width="120">Aksi</th>
</tr>
</thead>

<tbody>

<?php 
$no = 1;
$total = 0; 
?>

<?php $__empty_1 = true; $__currentLoopData = $keranjang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

<?php
$harga = $k->obat->harga_beli ?? 0;
$subtotal = $k->qty * $harga;
$total += $subtotal;
?>

<tr>

<td><?php echo e($no++); ?></td>

<td>BL00<?php echo e($k->id); ?></td>

<td><?php echo e($k->supplier->kode_supplier ?? '-'); ?></td>

<td><?php echo e($k->obat->kode_obat ?? '-'); ?></td>

<td><?php echo e(date('Y-m-d')); ?></td>

<td><?php echo e($k->qty); ?></td>

<td class="text-end">
Rp <?php echo e(number_format($subtotal)); ?>

</td>

<td>

<div class="d-flex justify-content-center align-items-center gap-2">

<input 
type="checkbox"
name="keranjang_id[]"
value="<?php echo e($k->id); ?>"
class="form-check-input checkbox-hijau checkItem"
data-subtotal="<?php echo e($subtotal); ?>"
>

<button type="button"
onclick="hapusKeranjang(<?php echo e($k->id); ?>)"
class="btn btn-sm btn-outline-danger">

<i class="bi bi-trash"></i>

</button>

</div>

</td>

</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

<tr>
<td colspan="8" class="text-muted text-center">
Keranjang masih kosong
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>

<!-- TOTAL -->
<div class="d-flex justify-content-end mt-3">

<h5 class="text-success" style="font-size:16px;">
Total : <span id="totalSemua">Rp 0</span>
</h5>

</div>

</div>

<!-- FOOTER -->
<div class="modal-footer">

<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
<i class="bi bi-x-circle"></i> Tutup
</button>

<button type="submit" class="btn btn-success btn-sm">
<i class="bi bi-check-circle"></i> Checkout Terpilih
</button>

</div>

</form>

</div>
</div>
</div>


<!-- FORM HAPUS TERSEMBUNYI -->
<form id="formHapusKeranjang" method="POST">
<?php echo csrf_field(); ?>
<?php echo method_field('DELETE'); ?>
</form>


<script>

function hapusKeranjang(id){

Swal.fire({
title: 'Yakin hapus?',
text: 'Item akan dihapus dari keranjang',
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#d33',
cancelButtonColor: '#6c757d',
confirmButtonText: 'Ya, hapus',
cancelButtonText: 'Batal'
}).then((result)=>{

if(result.isConfirmed){

let form = document.getElementById('formHapusKeranjang');

form.action = '/admin/keranjang/' + id;

form.submit();

}

});

}


function formatRupiah(angka){
return new Intl.NumberFormat('id-ID',{
style:'currency',
currency:'IDR',
minimumFractionDigits:0
}).format(angka);
}


function hitungTotalKeranjang(){

let total = 0;

document.querySelectorAll('.checkItem:checked').forEach(function(item){

let subtotal = parseInt(item.getAttribute('data-subtotal')) || 0;

total += subtotal;

});

document.getElementById('totalSemua').innerText = formatRupiah(total);

}


document.querySelectorAll('.checkItem').forEach(function(item){

item.addEventListener('change', function(){

hitungTotalKeranjang();

});

});

</script><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/data_pembelian/modal_keranjang.blade.php ENDPATH**/ ?>