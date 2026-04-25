@extends('admin.layout.app')

@section('title')
Profil Admin
@endsection

@section('content')

<style>

.profile-wrapper{
max-width:1100px;
margin:auto;
}

.profile-card{
border-radius:12px;
}

.profile-avatar{
width:130px;
height:130px;
border-radius:50%;
object-fit:cover;
border:4px solid #f1f1f1;
}

</style>

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

<div class="profile-wrapper">

<div class="row g-4">

<!-- PROFILE INFO -->
<div class="col-md-4">

<div class="card profile-card shadow-sm border-0 text-center">

<div class="card-body p-4">

<img
src="{{ $user->foto ? asset('storage/foto/'.$user->foto) : asset('logo/user.png') }}"
class="profile-avatar mb-3"
>

<h5 class="mb-1 fw-semibold">{{ $user->name }}</h5>

<span class="badge bg-success text-capitalize mb-3">
{{ $user->role }}
</span>

<hr>

<p class="text-muted small mb-0">
Kelola informasi profil akun anda.  
Pastikan data selalu diperbarui.
</p>

</div>
</div>
</div>


<!-- FORM EDIT -->
<div class="col-md-8">

<div class="card profile-card shadow-sm border-0">

<div class="card-body p-4">

<h5 class="mb-4 fw-semibold">
<i class="bi bi-person-gear me-2"></i>
Edit Profil
</h5>

<form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">

@csrf

<div class="row">

<div class="col-md-12 mb-3">
<label class="form-label">Foto Profil</label>
<input type="file" name="foto" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Nama</label>
<input type="text" name="name" class="form-control" value="{{ $user->name }}">
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Role</label>
<input type="text" class="form-control" value="{{ $user->role }}" readonly>
</div>

<div class="col-md-12 mb-4">
<label class="form-label">Password Baru</label>
<input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diganti">
</div>

<div class="col-md-12">

<button class="btn btn-success px-4">
<i class="bi bi-check-circle me-1"></i>
Update Profil
</button>

</div>

</div>

</form>

</div>
</div>
</div>

</div>
</div>

@endsection