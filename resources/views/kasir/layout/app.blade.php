<!DOCTYPE html>
<html>

<head>

<title>Kasir - Apotek Zema</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- DATATABLE -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

@stack('styles')

<style>

/* (SEMUA CSS ANDA TIDAK SAYA UBAH SAMA SEKALI) */

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
background:#117345;
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
background:#20a827;
transform:translateX(6px);
}

.sidebar .nav-link.active{
background:#20a827;
transform:translateX(6px);
}

.sidebar .nav-link i{
font-size:17px;
}

.menu-title{
font-size:12px;
color:#b7d3b9;
margin-top:12px;
margin-bottom:4px;
letter-spacing:1px;
}

.main{
flex:1;
display:flex;
flex-direction:column;
}

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

.content{
flex:1;
overflow-y:auto;
padding:30px;
scrollbar-width:none;
}

.content::-webkit-scrollbar{
display:none;
}

/* BUTTON PROFIL AGAR TIDAK BIRU */

.user-profile:focus,
.user-profile:active,
.user-profile.show{
border-color:#198754 !important;
box-shadow:0 0 0 0.2rem rgba(25,135,84,0.25) !important;
background:#f8f9fa !important;
color:#198754 !important;
}

/* DROPDOWN ITEM HOVER JUGA HIJAU */

.dropdown-item:hover{
background:#e9f7ef;
color:#198754;
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
<a class="nav-link {{ request()->is('kasir/dashboard') ? 'active' : '' }}" href="/kasir/dashboard">
<i class="bi bi-speedometer2"></i>
Dashboard
</a>
</li>

<hr>

<div class="menu-title">TRANSAKSI</div>

<li>
<a class="nav-link {{ request()->is('kasir/penjualan*') ? 'active' : '' }}" href="/kasir/penjualan">
<i class="bi bi-cart"></i>
Data Penjualan
</a>
</li>

<li>
<a class="nav-link {{ request()->is('kasir/riwayat*') ? 'active' : '' }}" href="/kasir/riwayat">
<i class="bi bi-clock-history"></i>
Riwayat Penjualan
</a>
</li>

</ul>

</div>

</div>


<!-- MAIN -->
<div class="main">

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


<!-- USER -->
<div class="dropdown">

<button class="btn btn-light dropdown-toggle user-profile" data-bs-toggle="dropdown">

<img src="{{ Auth::user()->foto ? asset('storage/foto/'.Auth::user()->foto) : asset('logo/user.png') }}">

<span>{{ Auth::user()->role }}</span>

</button>

<ul class="dropdown-menu dropdown-menu-end">

<li>
<a class="dropdown-item" href="/kasir/profile">
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

<div class="content">

@yield('content')

</div>

</div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@stack('scripts')

</body>
</html>