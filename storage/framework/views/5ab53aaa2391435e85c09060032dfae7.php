

<?php $__env->startSection('title'); ?>
Data Pembelian
<?php $__env->stopSection(); ?>

<?php if(session('success')): ?>
<script>
Swal.fire({
icon:'success',
title:'Berhasil',
text:'<?php echo e(session('success')); ?>',
timer:2000,
showConfirmButton:false
});
</script>
<?php endif; ?>


<?php if(session('error')): ?>
<script>
Swal.fire({
icon:'error',
title:'Gagal',
text:'<?php echo e(session('error')); ?>'
});
</script>
<?php endif; ?>

<?php $__env->startSection('content'); ?>

<style>
#tabelPembelian{
font-size:13px;
}

#tabelPembelian th{
font-size:12px;
padding:6px;
}

#tabelPembelian td{
padding:5px;
}
</style>

<div class="d-flex justify-content-end align-items-center mb-3">

<div>

<button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
<i class="bi bi-plus-circle"></i> Tambah Data
</button>

<button class="btn btn-danger position-relative" data-bs-toggle="modal" data-bs-target="#modalKeranjang">

<i class="bi bi-cart3"></i>

<?php if($keranjang->count() > 0): ?>
<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
<?php echo e($keranjang->count()); ?>

</span>
<?php endif; ?>

</button>

</div>

</div>


<div class="card">
<div class="card-body">

<table id="tabelPembelian" class="table table-bordered text-center align-middle">

<thead class="header-hijau text-center align-top">

<tr>

<th width="60">No</th>
<th>No Transaksi</th>
<th>Tanggal Transaksi</th>
<th>Nama Supplier</th>
<th>Nama Obat</th>
<th>Tanggal EXP</th>
<th>Harga</th>
<th>Jumlah Beli</th>
<th>Total Biaya</th>
<th width="120">Aksi</th>

</tr>

</thead>

<tbody>

<?php $no = 1; ?>

<?php $__currentLoopData = $pembelian; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <?php $__currentLoopData = $p->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <tr>

        <td class="text-center"><?php echo e($no++); ?></td>

        <td class="text-center"><?php echo e($p->kode_transaksi ?? '-'); ?></td>

        <td class="text-center"><?php echo e($p->tanggal); ?></td>

        <td class="text-start"><?php echo e(optional($p->supplier)->nama_supplier ?? '-'); ?></td>

        <td class="text-start"><?php echo e(optional($d->obat)->nama_obat ?? '-'); ?></td>

        <td class="text-center"><?php echo e(optional($d->obat)->tanggal_exp ?? '-'); ?></td>

        <td class="text-end">Rp <?php echo e(number_format($d->harga)); ?></td>

        <td class="text-center"><?php echo e($d->jumlah); ?></td>

        <td class="text-end">Rp <?php echo e(number_format($d->subtotal)); ?></td>

        <td class="text-center">

    <!-- CETAK -->
    <a href="<?php echo e(route('pembelian.cetak',$p->id)); ?>" 
    class="btn btn-sm btn-outline-primary"
    target="_blank">

    <i class="bi bi-printer"></i>

    </a>

    <!-- HAPUS -->
<form id="delete-form-<?php echo e($p->id); ?>" action="<?php echo e(route('pembelian.destroy',$p->id)); ?>" method="POST" style="display:inline">

<?php echo csrf_field(); ?>
<?php echo method_field('DELETE'); ?>

<button type="button"
class="btn btn-sm btn-outline-danger"
onclick="confirmDelete(<?php echo e($p->id); ?>)">

<i class="bi bi-trash"></i>

</button>

</form>

</td>

    </tr>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>

</div>
</div>

<?php echo $__env->make('admin.data_pembelian.modal_tambah', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('admin.data_pembelian.modal_keranjang', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startPush('scripts'); ?>

<script>

$(document).ready(function(){

$('#tabelPembelian').DataTable({

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

$(document).ready(function(){

$('.select2').select2({
dropdownParent: $('#modalTambah'),
width:'100%'
});

});


// isi otomatis data obat

$('#obat').on('change', function(){

let selected = $(this).find(':selected');

let kode = selected.data('kode');
let harga = selected.data('harga');

$('#kode_obat').val(kode);
$('#harga').val(formatRupiah(harga));

hitungTotal();

cekExp();

});


// hitung total otomatis

$('#jumlah').on('keyup change', function(){

hitungTotal();

});

function hitungTotal(){

let harga = $('#harga').val().replace(/\D/g,'');
let jumlah = $('#jumlah').val();

if(harga && jumlah){

let total = harga * jumlah;

$('#total').val(formatRupiah(total));

}

}


// format rupiah

function formatRupiah(angka){

return new Intl.NumberFormat('id-ID',{
style:'currency',
currency:'IDR',
minimumFractionDigits:0
}).format(angka);

}


// cek exp hampir kadaluarsa

function cekExp(){

let exp = new Date($('#exp').val());
let today = new Date();

let selisih = (exp - today) / (1000*60*60*24);

if(selisih < 90){

$('#exp').css({
'background':'#ffe5e5',
'color':'red',
'font-weight':'600'
});

}

}

// centang semua
$('#checkAll').on('click', function(){

$('.checkItem').prop('checked', this.checked);

});

$('#formTambah').on('submit', function(e){

let supplier = $('[name=supplier_id]').val();
let obat = $('#obat').val();
let qty = $('#jumlah').val();

if(!supplier || !obat || !qty){

e.preventDefault();

Swal.fire({
icon:'warning',
title:'Data belum lengkap',
text:'Semua field wajib diisi!'
});

}

});

$('#formCheckout').on('submit', function(e){

if($('input[name="keranjang_id[]"]:checked').length == 0){

e.preventDefault();

Swal.fire({
icon:'warning',
title:'Pilih Data',
text:'Centang minimal 1 item keranjang'
});

}

});

</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/data_pembelian/index.blade.php ENDPATH**/ ?>