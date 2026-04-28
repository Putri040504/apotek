

<?php $__env->startSection('title'); ?>
Dashboard Admin
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>

<style>

body{
background:#f5f7f9;
}

/* CARD */
.card{
border-radius:18px;
border:3px solid rgba(25,135,84,0.45);
background:white;
box-shadow:0 6px 15px rgba(0,0,0,0.08);
transition:all .3s ease;
cursor:pointer;
}

.card:hover{
transform:translateY(-4px);
box-shadow:
0 8px 20px rgba(0,0,0,0.1),
0 0 0 4px rgba(25,135,84,0.15);
}

/* HEADER */
.card-header{
border-bottom:2px solid rgba(25,135,84,0.25);
background:#198754;
color:white;
font-weight:600;
}

/* TABLE */
.table thead{
background:#198754;
color:white;
}

.table tbody tr:hover{
background:#f1fdf6;
}

</style>

<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<div class="container-fluid">

<div class="row g-4">

<!-- CARD 1 -->
<div class="col-md-4">
<div class="card">
<div class="card-body d-flex justify-content-between align-items-center">
<div>
<small class="text-muted">Total Obat</small>
<h4 class="fw-bold mb-0 counter" data-target="<?php echo e($total_obat); ?>">0</h4>
</div>
<i class="bi bi-capsule fs-2 text-success"></i>
</div>
</div>
</div>

<!-- CARD 2 -->
<div class="col-md-4">
<div class="card shadow-sm">
<div class="card-body d-flex justify-content-between align-items-center">
<div>
<small class="text-muted">Total Supplier</small>
<h4 class="fw-bold mb-0 counter" data-target="<?php echo e($total_supplier); ?>">0</h4>
</div>
<i class="bi bi-truck fs-2 text-primary"></i>
</div>
</div>
</div>

<!-- CARD 3 -->
<div class="col-md-4">
<div class="card shadow-sm">
<div class="card-body d-flex justify-content-between align-items-center">
<div>
<small class="text-muted">Total Pengguna</small>
<h4 class="fw-bold mb-0 counter" data-target="<?php echo e($total_user); ?>">0</h4>
</div>
<i class="bi bi-people fs-2 text-warning"></i>
</div>
</div>
</div>

<!-- CARD 4 -->
<div class="col-md-4">
<div class="card shadow-sm">
<div class="card-body d-flex justify-content-between align-items-center">
<div>
<small class="text-muted">Obat Kadaluarsa</small>
<h4 class="fw-bold text-danger mb-0">
<?php echo e($obat_kadaluarsa); ?>

</h4>
</div>
<i class="bi bi-exclamation-triangle fs-2 text-danger"></i>
</div>
</div>
</div>

<!-- CARD 5 -->
<div class="col-md-4">
<div class="card shadow-sm">
<div class="card-body d-flex justify-content-between align-items-center">
<div>
<small class="text-muted">Total Transaksi</small>
<h4 class="fw-bold mb-0 counter" data-target="<?php echo e($total_transaksi); ?>">0</h4>
</div>
<i class="bi bi-cash-stack fs-2 text-danger"></i>
</div>
</div>
</div>

<!-- CARD 6 -->
<div class="col-md-4">
<div class="card shadow-sm">
<div class="card-body d-flex justify-content-between align-items-center">
<div>
<small class="text-muted">Total Nilai Stok</small>
<h4 class="fw-bold text-success mb-0">
Rp <?php echo e(number_format($total_nilai_stok)); ?>

</h4>
</div>
<i class="bi bi-graph-up fs-2 text-success"></i>
</div>
</div>
</div>

<!-- CARD 7 -->
<div class="col-md-4">
<div class="card shadow-sm">
<div class="card-body d-flex justify-content-between align-items-center">
<div>
<small class="text-muted">Pembelian Hari Ini</small>
<h4 class="fw-bold text-primary mb-0">
Rp <?php echo e(number_format($pembelian_hari_ini)); ?>

</h4>
</div>
<i class="bi bi-cart-plus fs-2 text-primary"></i>
</div>
</div>
</div>

<!-- CARD 8 -->
<div class="col-md-4">
<div class="card shadow-sm">
<div class="card-body d-flex justify-content-between align-items-center">
<div>
<small class="text-muted">Penjualan Hari Ini</small>
<h4 class="fw-bold text-success mb-0">
Rp <?php echo e(number_format($penjualan_hari_ini)); ?>

</h4>
</div>
<i class="bi bi-cart-check fs-2 text-success"></i>
</div>
</div>
</div>

<!-- CARD 9 -->
<div class="col-md-4">
<div class="card shadow-sm">
<div class="card-body d-flex justify-content-between align-items-center">
<div>
<small class="text-muted">Profit Hari Ini</small>
<h4 class="fw-bold text-success mb-0">
Rp <?php echo e(number_format($profit_hari_ini)); ?>

</h4>
</div>
<i class="bi bi-currency-dollar fs-2 text-success"></i>
</div>
</div>
</div>

</div>

</div>


<!-- GRAFIK DAN FEFO -->
<div class="row mt-4 g-4">

<!-- GRAFIK -->
<div class="col-md-6">
<div class="card shadow-sm">

<div class="card-header bg-success text-white">
Grafik Penjualan & Pembelian
</div>

<div class="card-body">
<canvas id="chartPenjualan"></canvas>
</div>

</div>
</div>

<!-- PRIORITAS PENJUALAN FEFO -->
<div class="col-md-6">

<div class="card shadow-sm">

<div class="card-header bg-success text-white">
Prioritas Penjualan Obat (FEFO)
</div>

<div class="card-body">

<div class="mb-3 small">
<span class="badge bg-danger">Exp < 30 hari</span>
<span class="badge bg-warning text-dark">Exp < 90 hari</span>
<span class="badge bg-success">Aman</span>
</div>

<table class="table table-sm table-striped table-hover">

<thead>
<tr>
<th>Nama Obat</th>
<th>Stok</th>
<th>Tanggal Expired</th>
</tr>
</thead>

<tbody>

<?php $__currentLoopData = $prioritas_fefo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $obat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr>

<td><?php echo e($obat->nama_obat); ?></td>

<td>
<span class="badge bg-primary">
<?php echo e($obat->stok); ?>

</span>
</td>

<td>

<?php
$exp = \Carbon\Carbon::parse($obat->tanggal_exp);
$today = now();
$diff = $today->diffInDays($exp, false);
?>

<?php if($diff <= 30): ?>

<span class="badge bg-danger">
<?php echo e($exp->format('d M Y')); ?>

</span>

<?php elseif($diff <= 90): ?>

<span class="badge bg-warning text-dark">
<?php echo e($exp->format('d M Y')); ?>

</span>

<?php else: ?>

<span class="badge bg-success">
<?php echo e($exp->format('d M Y')); ?>

</span>

<?php endif; ?>

</td>

</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>

</div>

</div>

</div>

</div>


<!-- STOK DAN KADALUARSA -->
<div class="row mt-4 g-4">

<div class="col-md-6">

<div class="card shadow-sm">

<div class="card-header bg-success text-white">
Stok Hampir Habis
</div>

<div class="card-body">

<ul class="list-group list-group-flush small">

<?php $__currentLoopData = $stok_menipis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $obat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<li class="list-group-item d-flex justify-content-between">

<?php echo e($obat->nama_obat); ?>


<span class="badge bg-danger">
<?php echo e($obat->stok); ?>

</span>

</li>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</ul>

</div>

</div>

</div>


<div class="col-md-6">

<div class="card shadow-sm">

<div class="card-header bg-success text-white">
Obat Hampir Kadaluarsa
</div>

<div class="card-body">

<ul class="list-group list-group-flush small">

<?php $__currentLoopData = $obat_hampir_kadaluarsa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $obat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<li class="list-group-item d-flex justify-content-between">

<?php echo e($obat->nama_obat); ?>


<span class="badge bg-warning text-dark">
<?php echo e(\Carbon\Carbon::parse($obat->tanggal_exp)->format('d M Y')); ?>

</span>

</li>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</ul>

</div>

</div>

</div>

</div>


<!-- TOP OBAT DAN TRANSAKSI -->
<div class="row mt-4 g-4">

<div class="col-md-6">

<div class="card shadow-sm">

<div class="card-header bg-success text-white">
Top 5 Obat Terlaris
</div>

<div class="card-body">

<table class="table table-sm">

<thead>
<tr>
<th>Obat</th>
<th>Total</th>
</tr>
</thead>

<tbody>

<?php $__currentLoopData = $obat_terlaris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr>
<td><?php echo e($item->nama_obat); ?></td>
<td><?php echo e($item->total); ?></td>
</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>

</div>

</div>

</div>


<div class="col-md-6">

<div class="card shadow-sm">

<div class="card-header bg-success text-white">
Transaksi Terbaru
</div>

<div class="card-body">

<table class="table table-sm">

<thead>
<tr>
<th>Tanggal</th>
<th>Total</th>
</tr>
</thead>

<tbody>

<?php $__currentLoopData = $transaksi_terbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr>
<td><?php echo e(\Carbon\Carbon::parse($trx->created_at)->format('d M Y')); ?></td>
<td class="text-success">
Rp <?php echo e(number_format($trx->total)); ?>

</td>
</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<script>

const ctx = document.getElementById('chartPenjualan');

new Chart(ctx,{
type:'line',
data:{
labels:<?php echo json_encode($label, 15, 512) ?>,
datasets:[
{
label:'Penjualan',
data:<?php echo json_encode($data_penjualan, 15, 512) ?>,
borderColor:'#ff4da6', // pink
backgroundColor:'rgba(255,77,166,0.2)',
borderWidth:3,
tension:0.4,
fill:true,
pointRadius:0
},
{
label:'Pembelian',
data:<?php echo json_encode($data_pembelian, 15, 512) ?>,
borderColor:'#3b82f6', // biru
backgroundColor:'rgba(59,130,246,0.2)',
borderWidth:3,
tension:0.4,
fill:true,
pointRadius:0
}
]
},
options:{
responsive:true,
plugins:{
legend:{
display:true
}
},
scales:{
y:{
beginAtZero:true
}
}
}
});

</script>

<script>

const counters = document.querySelectorAll('.counter');

counters.forEach(counter => {

const updateCounter = () => {

const target = +counter.getAttribute('data-target');
const current = +counter.innerText;

const increment = target / 80;

if(current < target){
counter.innerText = Math.ceil(current + increment);
setTimeout(updateCounter,20);
}else{
counter.innerText = target;
}

};

updateCounter();

});

</script>

<?php $__env->stopPush(); ?>

<style>

body{
    background:#f5f7f9;
}

/* CARD */
.card{
    border-radius:14px;
    border:2px solid rgba(25,135,84,0.45);
    box-shadow:0 6px 15px rgba(0,0,0,0.08) !important;
    transition:all .3s ease;
    cursor:pointer;
}

/* HOVER */
.card:hover{
    transform:translateY(-6px);
    box-shadow:
        0 12px 25px rgba(0,0,0,0.12),
        0 0 15px rgba(25,135,84,0.35),
        0 0 30px rgba(25,135,84,0.25),
        0 0 50px rgba(25,135,84,0.15) !important;
}

/* HEADER CARD */
.card-header{
    border-bottom:2px solid rgba(25,135,84,0.25);
    font-weight:600;
    background:#198754 !important;
    color:white !important;
}

/* TABLE */
.table{
    border-radius:10px;
    overflow:hidden;
}

.table thead{
    background:#198754;
    color:white;
}

.table thead th{
    font-weight:600;
    text-align:center;
}

.table tbody tr:hover{
    background:#f1fdf6;
    transition:.2s;
}

/* CARD TEXT */
.card-body h3{
font-size:20px;
font-weight:600;
margin-top:4px;
}

.card-body small{
font-size:12px;
}

/* ICON */
.card-body i{
font-size:28px !important;
opacity:0.8;
}

/* TABLE SIZE */
.table{
font-size:13px;
}

.table th{
font-size:12px;
padding:6px;
}

.table td{
font-size:12px;
padding:6px;
}

/* CARD HEADER */
.card-header{
font-size:14px;
padding:10px 14px;
}

.card:hover{
    transform:translateY(-4px);
    box-shadow:
        0 8px 20px rgba(0,0,0,0.1),
        0 0 0 4px rgba(25,135,84,0.15);
}
.card{
    border-radius:18px;
    border:3px solid rgba(25,135,84,0.45);
    background:white;
    box-shadow:0 6px 15px rgba(0,0,0,0.08) !important;
    transition:all .3s ease;
    cursor:pointer;
}
.card small{
font-size:13px;
}

.card h4{
font-size:20px;
}
</style>
<?php echo $__env->make('admin.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Server\Project gueh\apotek-zema\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>