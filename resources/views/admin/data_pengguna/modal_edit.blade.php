<div class="modal fade" id="modalEdit{{ $user->id }}">

<div class="modal-dialog">
<div class="modal-content">

<form action="{{ route('pengguna.update',$user->id) }}" method="POST">

@csrf
@method('PUT')

<div class="modal-header bg-success text-white">

<h5 class="modal-title">
<i class="bi bi-pencil-square me-2"></i> Edit Pengguna
</h5>

<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="mb-3">
<label>Nama</label>
<input type="text" name="name" value="{{ $user->name }}" class="form-control">
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" value="{{ $user->email }}" class="form-control">
</div>

<div class="mb-3">
<label>Role</label>
<select name="role" class="form-control">

<option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
Admin
</option>

<option value="kasir" {{ $user->role == 'kasir' ? 'selected' : '' }}>
Kasir
</option>

</select>
</div>

</div>

<div class="modal-footer">

<button class="btn btn-success">
<i class="bi bi-save"></i> Update
</button>

<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
Batal
</button>

</div>

</form>

</div>
</div>
</div>