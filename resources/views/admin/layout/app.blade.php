<!DOCTYPE html>
<html>

<head>

<title>Admin - Apotek Zema</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{ asset('css/barcode-scanner.css') }}" rel="stylesheet">
<link href="{{ asset('css/barcode-print.css') }}" rel="stylesheet">

@stack('styles')

<style>

body{
background:#f4f6f9;
overflow:hidden;
font-family:Segoe UI;
}

.wrapper{
display:flex;
height:100vh;
}

/* SIDEBAR */

.sidebar{
width:260px;
background: #117345;
color:white;
display:flex;
flex-direction:column;
}

/* SCROLL */

.sidebar-menu{
flex:1;
overflow-y:auto;
padding:18px;
scrollbar-width:none;
}

.sidebar-menu::-webkit-scrollbar{
display:none;
}

/* LOGO */

.sidebar-logo{
display:flex;
align-items:center;
gap:20px;
margin-bottom:15px;
}

.sidebar-logo img{
width:50px;
height:auto;
margin-left:10px;
}

.sidebar-title{
font-weight:700;
font-size:18px;
line-height:1.2;
}

/* MENU */

.sidebar .nav-link{
color:white;
padding:9px 12px;
border-radius:6px;
font-size:15px;
display:flex;
align-items:center;
gap:10px;
transition:all 0.3s ease;
}

.sidebar .nav-link:hover{
background: #20a827;
transform:translateX(6px);
}

/* MENU ACTIVE */

.sidebar .nav-link.active{
background:#20a827;
transform:translateX(6px);
}

.sidebar .nav-link i{
font-size:17px;
}


/* MENU TITLE */

.menu-title{
font-size:12px;
color:#b7d3b9;
margin-top:12px;
margin-bottom:4px;
letter-spacing:1px;
}

/* MAIN */

.main{
flex:1;
display:flex;
flex-direction:column;
}

/* HEADER */

.topbar{
background:white;
padding:15px 30px;
display:flex;
align-items:center;
justify-content:flex-end;
position:relative;
border-bottom:3px solid #198754;
box-shadow:0 4px 10px rgba(28,148,62,0.08);
z-index:10;
}

.page-title{
position:absolute;
left:45%;
transform:translateX(-50%);
font-weight:700;
color:#198754;
margin:0;
font-size:22px;
}

/* USER */

.user-menu{
margin-left:15px;
}

.user-profile{
display:flex;
align-items:center;
gap:8px;
}

.user-profile img{
width:32px;
height:32px;
border-radius:50%;
object-fit:cover;
}

/* CONTENT */

.content{
flex:1;
overflow-y:auto;
padding:30px;
scrollbar-width:none;
}

.content::-webkit-scrollbar{
display:none;
}

/* COLLAPSE ARROW */

.arrow{
transition:0.3s;
font-size:23px;
font-weight:bold;
}

a[aria-expanded="true"] .arrow{
transform:rotate(90deg);
}

/* pagination datatables bootstrap */

.dataTables_wrapper .dataTables_paginate .page-link{
color:#198754 !important;
}

.dataTables_wrapper .dataTables_paginate .page-item.active .page-link{
background:#198754 !important;
border-color:#198754 !important;
color:white !important;
}

.dataTables_wrapper .dataTables_paginate .page-link:hover{
background:#157347 !important;
border-color:#157347 !important;
color:white !important;
}
.dataTables_filter input{
border-radius:6px;
border:1px solid #198754;
}

.dataTables_filter input:focus{
box-shadow:0 0 0 0.2rem rgba(25,135,84,.25);
border-color:#198754;
}

/* angka pagination */

.dataTables_wrapper .dataTables_paginate .page-link{
color:#198754 !important;
}

/* tombol aktif */

.dataTables_wrapper .dataTables_paginate .page-item.active .page-link{
background:#198754 !important;
border-color:#198754 !important;
color:white !important;
}

/* hover */

.dataTables_wrapper .dataTables_paginate .page-link:hover{
background:#198754 !important;
border-color:#198754 !important;
color:white !important;
}

/* dropdown tampilkan data */

.dataTables_length select{
border:1px solid #198754 !important;
border-radius:6px;
}

/* saat diklik */

.dataTables_length select:focus{
border-color:#198754 !important;
box-shadow:0 0 0 0.2rem rgba(25,135,84,.25) !important;
outline:none;
}

.form-control:focus{
border-color:#198754;
box-shadow:0 0 0 0.2rem rgba(25,135,84,0.25);
}

/* hilangkan efek biru bootstrap */

.page-link:focus{
outline:none !important;
box-shadow:none !important;
border-color:#198754 !important;
}

/* warna saat klik */

.page-link:active{
background:#198754 !important;
border-color:#198754 !important;
color:white !important;
}

/* pagination aktif */

.page-item.active .page-link{
background:#198754 !important;
border-color:#198754 !important;
color:white !important;
}

/* hover */

.page-link:hover{
background:#198754 !important;
border-color:#198754 !important;
color:white !important;
}

/* header tabel apotek */

.header-hijau{
background:#198754;
color:white;
}

.header-hijau th{
background:#198754 !important;
color:white !important;
}

#tabelSupplier thead th{
text-align: left !important;
vertical-align: top !important;
}

.header-hijau th{
    vertical-align: top;
    padding:12px;
}

.auto-field{
background:#f5f5f5;
cursor:not-allowed;
color:#6c757d;
}

.auto-field:focus{
background:#f5f5f5;
box-shadow:none;
border-color:#ced4da;
}

.checkbox-hijau{
    width:22px;
    height:22px;
    cursor:pointer;
    accent-color:#198754; /* warna centang */
}

/* hilangkan efek biru bootstrap */
.checkbox-hijau:focus{
    outline:none !important;
    box-shadow:none !important;
}

/* override bootstrap */
.form-check-input:checked{
    background-color: #198754 !important;
    border-color: #198754 !important;
}

.form-check-input:focus{
    box-shadow:none !important;
}

/* warna border */
.checkbox-hijau{
    width:30px;
    height:30px;
    accent-color: #198754;
    border:1px solid #198754;
}

/* saat select aktif */
.form-select:focus{
border-color:#198754;
box-shadow:0 0 0 0.2rem rgba(25,135,84,0.25);
}

/* option yang dipilih */
.form-select option:checked{
background-color:#198754;
color:white;
}

/* hover option (beberapa browser support) */
.form-select option:hover{
background-color:#20c997;
color:white;
}

</style>
</head>

<body>

<div class="wrapper">

<!-- SIDEBAR -->
<div class="sidebar">

<div class="sidebar-menu">

<div class="sidebar-logo">

<img src="{{ asset('logo/apotek zema.png') }}">

<div class="sidebar-title">
APOTEK ZEMA
</div>

</div>

<hr>

<ul class="nav flex-column">

<div class="menu-title">MAIN MENU</div>

<li>
<a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="/admin/dashboard">
<i class="bi bi-speedometer2"></i>
Dashboard
</a>
</li>

<hr>

<div class="menu-title">MASTER DATA</div>

<li>
<a class="nav-link {{ request()->is('admin/kategori*') ? 'active' : '' }}" href="/admin/kategori">
<i class="bi bi-tags"></i>
Data Kategori
</a>
</li>

<li>
<a class="nav-link {{ request()->is('admin/obat*') ? 'active' : '' }}" href="/admin/obat">
<i class="bi bi-capsule"></i>
Data Obat
</a>
</li>

<li>
<a class="nav-link {{ request()->is('admin/supplier*') ? 'active' : '' }}" href="/admin/supplier">
<i class="bi bi-truck"></i>
Data Supplier
</a>
</li>

<li>
<a class="nav-link {{ request()->is('admin/pengguna*') ? 'active' : '' }}" href="/admin/pengguna">
<i class="bi bi-people"></i>
Data Pengguna
</a>
</li>

<hr>

<div class="menu-title">MASTER TRANSAKSI</div>

<li>
<a class="nav-link {{ request()->is('admin/pembelian*') ? 'active' : '' }}" href="/admin/pembelian">
<i class="bi bi-cart-plus"></i>
Data Pembelian
</a>
</li>

<hr>

<div class="menu-title">MASTER LAPORAN</div>

<li>

<a class="nav-link d-flex justify-content-between align-items-center"
data-bs-toggle="collapse"
href="#laporanMenu">

<span>
<i class="bi bi-bar-chart"></i>
Data Laporan
</span>

<span class="arrow">›</span>

</a>

<div id="laporanMenu" class="collapse">

<ul class="nav flex-column ms-3">

<li>
<a class="nav-link {{ request()->is('admin/laporan/obat') ? 'active' : '' }}" href="/admin/laporan/obat">
<i class="bi bi-file-earmark"></i>
Data Obat
</a>
</li>

<li>

<a class="nav-link d-flex justify-content-between"
data-bs-toggle="collapse"
href="#pembelianMenu">

<span>
<i class="bi bi-cart"></i>
Pembelian
</span>

<span class="arrow">›</span>

</a>

<div id="pembelianMenu" class="collapse">

<ul class="nav flex-column ms-3">

<li>
<a class="nav-link {{ request()->is('admin/laporan/pembelian-bulanan') ? 'active' : '' }}" href="/admin/laporan/pembelian-bulanan">
Pembelian Bulanan
</a>
</li>

<li>
<a class="nav-link {{ request()->is('admin/laporan/pembelian-jenis') ? 'active' : '' }}" href="/admin/laporan/pembelian-jenis">
Jenis Obat
</a>
</li>

</ul>

</div>

</li>

<li>

<a class="nav-link d-flex justify-content-between"
data-bs-toggle="collapse"
href="#penjualanMenu">

<span>
<i class="bi bi-cash-stack"></i>
Penjualan
</span>

<span class="arrow">›</span>

</a>

<div id="penjualanMenu" class="collapse">

<ul class="nav flex-column ms-3">

<li>
<a class="nav-link {{ request()->is('admin/laporan/penjualan-bulanan') ? 'active' : '' }}" href="/admin/laporan/penjualan-bulanan">
Penjualan Bulanan
</a>
</li>

<li>
<a class="nav-link {{ request()->is('admin/laporan/penjualan-jenis') ? 'active' : '' }}" href="/admin/laporan/penjualan-jenis">
Jenis Obat
</a>
</li>

<li>
<a class="nav-link {{ request()->is('admin/laporan/penjualan') ? 'active' : '' }}" href="/admin/laporan/penjualan">
Semua Penjualan
</a>
</li>

</ul>

</div>

</li>

</ul>

</div>

</li>

</ul>

</div>

</div>


<!-- MAIN -->
<div class="main">

<!-- HEADER -->

<!-- HEADER -->
<div class="topbar">

<h3 class="page-title">
@yield('title')
</h3>

<!-- NOTIFIKASI -->
<div class="dropdown me-3">

<a class="nav-link position-relative" data-bs-toggle="dropdown" style="cursor:pointer">

<i class="bi bi-bell fs-5"></i>

@if(($stok_habis->count() ?? 0) + ($obat_expired->count() ?? 0) > 0)

<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

{{ ($stok_habis->count() ?? 0) + ($obat_expired->count() ?? 0) }}

</span>

@endif

</a>

<ul class="dropdown-menu dropdown-menu-end" style="width:300px">

@if(isset($stok_habis) && $stok_habis->count() > 0)

<li class="dropdown-header text-danger">
Stok Habis
</li>

@foreach($stok_habis as $obat)

<li class="dropdown-item text-danger">
<i class="bi bi-exclamation-triangle"></i>
{{ $obat->nama_obat }} - Stok Habis
</li>

@endforeach

@endif


@if(isset($obat_expired) && $obat_expired->count() > 0)

<li><hr class="dropdown-divider"></li>

<li class="dropdown-header text-warning">
Hampir Expired
</li>

@foreach($obat_expired as $obat)

<li class="dropdown-item text-warning">
<i class="bi bi-clock-history"></i>
{{ $obat->nama_obat }} - Exp {{ $obat->tanggal_exp }}
</li>

@endforeach

@endif


@if(($stok_habis->count() ?? 0) == 0 && ($obat_expired->count() ?? 0) == 0)

<li class="dropdown-item text-muted">
Tidak ada notifikasi
</li>

@endif

</ul>

</div>


<!-- USER MENU -->
<div class="dropdown user-menu">

<button class="btn btn-light dropdown-toggle user-profile" data-bs-toggle="dropdown">

<img src="{{ Auth::user()->foto ? asset('storage/foto/'.Auth::user()->foto) : asset('logo/user.png') }}">

<span>{{ Auth::user()->role }}</span>

</button>

<ul class="dropdown-menu dropdown-menu-end">

<li>
<a class="dropdown-item" href="/admin/profile">
<i class="bi bi-person"></i> Profil
</a>
</li>

<li><hr class="dropdown-divider"></li>

<li>

<form method="POST" action="{{ route('logout') }}">
@csrf

<button class="dropdown-item">
<i class="bi bi-box-arrow-right"></i> Logout
</button>

</form>

</li>

</ul>

</div>

</div>

<!-- CONTENT -->
<div class="content">

@yield('content')

@include('components.barcode-scanner-modal')
@include('components.barcode-print-modal')

</div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="{{ asset('js/barcode-scanner.js') }}"></script>
<script src="{{ asset('js/barcode-print.js') }}"></script>

@stack('scripts')

</body>
</html>