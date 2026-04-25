<style>

/* stok hampir habis */
.stok-habis{
border:2px solid #dc3545 !important;
color:#dc3545 !important;
font-weight:600;
background:#fff5f5;
}

/* exp sudah lewat */
.expired{
border:2px solid #dc3545 !important;
background:#fff5f5 !important;
color:#dc3545 !important;
font-weight:600;
}

/* field otomatis (tidak bisa diklik) */
.auto-field{
background:#e9ecef !important;
cursor:not-allowed !important;
color:#6c757d !important;
}

input:disabled{
cursor:not-allowed !important;
background:#e9ecef !important;
}

/* form disable */
.form-disabled{
background:#e9ecef !important;
cursor:not-allowed !important;
}

/* cursor jika disabled */
input:disabled{
cursor:not-allowed !important;
background:#e9ecef !important;
}

</style>


<div class="modal fade" id="modalTambah">

<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<div class="modal-header bg-success text-white">

<h5 class="modal-title">
<i class="bi bi-cart-plus"></i>
Tambah Penjualan
</h5>

<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

</div>

<form action="{{ route('keranjang.store') }}" method="POST">

@csrf

<div class="modal-body">

<div class="row g-2">

<!-- NAMA OBAT -->
<div class="col-md-6">
<label class="form-label small">Nama Obat</label>

<select name="obat_id" class="form-control form-control-sm select2" required>

<option value="">Pilih Obat</option>

@foreach($obat as $o)

@if(\Carbon\Carbon::parse($o->tanggal_exp)->isFuture())

<option value="{{ $o->id }}"
data-harga="{{ $o->harga_jual }}"
data-exp="{{ $o->tanggal_exp }}"
data-stok="{{ $o->stok }}">

{{ $o->nama_obat }}

</option>

@endif

@endforeach

</select>
</div>


<!-- HARGA -->
<div class="col-md-6">
<label class="form-label small">Harga</label>
<input type="text" id="harga" class="form-control form-control-sm auto-field" disabled>
</div>


<!-- JUMLAH -->
<div class="col-md-6">
<label class="form-label small">Jumlah</label>
<input type="number" name="jumlah" id="jumlah" class="form-control form-control-sm" required>
</div>


<!-- TANGGAL EXP -->
<div class="col-md-6">
<label class="form-label small">Tanggal Exp</label>
<input type="text" id="kode_exp" class="form-control form-control-sm auto-field" disabled>
</div>


<!-- STOK -->
<div class="col-md-6">
<label class="form-label small">Stok Obat</label>
<input type="text" id="stok" class="form-control form-control-sm auto-field" disabled>
</div>


<!-- TOTAL -->
<div class="col-md-6">
<label class="form-label small">Total</label>
<input type="text" id="total" class="form-control form-control-sm auto-field" disabled>
</div>

</div>
</div>


<div class="modal-footer">

<button type="submit" class="btn btn-success btn-sm">
Tambah Keranjang
</button>

</div>

</form>

</div>
</div>
</div>


<script>

function formatRupiah(angka){
return new Intl.NumberFormat('id-ID',{
style:'currency',
currency:'IDR',
minimumFractionDigits:0
}).format(angka);
}


/* ambil elemen */
const obatSelect = document.querySelector('[name="obat_id"]');
const hargaInput = document.getElementById('harga');
const expInput = document.getElementById('kode_exp');
const stokInput = document.getElementById('stok');
const jumlahInput = document.getElementById('jumlah');
const totalInput = document.getElementById('total');
const formKeranjang = document.querySelector('#modalTambah form');


/* =========================
PILIH OBAT
========================= */

obatSelect.addEventListener('change', function(){

jumlahInput.disabled = false;

let selected = this.options[this.selectedIndex];

let harga = selected.dataset.harga || 0;
let exp = selected.dataset.exp || '';
let stok = parseInt(selected.dataset.stok || 0);

hargaInput.value = formatRupiah(harga);
expInput.value = exp;
stokInput.value = stok;

jumlahInput.max = stok;

/* hanya tampilkan warna saja */
if(stok <= 5){
stokInput.classList.add('stok-habis');
}

let today = new Date();
let expDate = new Date(exp);

let diff = (expDate - today) / (1000*60*60*24);

if(diff <= 30){
expInput.classList.add('expired');
}

hitungTotal();

});


/* =========================
CEK STOK
========================= */

function cekStok(stok){

stokInput.classList.remove('stok-habis');

if(stok <= 5){

stokInput.classList.add('stok-habis');

}

if(stok === 0){

jumlahInput.disabled = true;
jumlahInput.classList.add('form-disabled');

}

}


/* =========================
CEK EXPIRED
========================= */

function cekExp(exp){

if(!exp) return;

let today = new Date();
today.setHours(0,0,0,0);

let expDate = new Date(exp);
expDate.setHours(0,0,0,0);

let diff = (expDate - today) / (1000*60*60*24);

/* reset warna */
expInput.classList.remove('expired');

if(diff <= 30){

expInput.classList.add('expired');

/* modal alert */
Swal.fire({
icon:'error',
title:'Obat Tidak Bisa Dijual',
text:'Obat sudah expired atau mendekati expired (≤ 30 hari)',
confirmButtonText:'OK'
});

/* disable input jumlah */
jumlahInput.disabled = true;
jumlahInput.classList.add('form-disabled');
jumlahInput.style.cursor = "not-allowed";

}

}

/* =========================
VALIDASI JUMLAH
========================= */

jumlahInput.addEventListener('input', function(){

let stok = parseInt(stokInput.value || 0);
let jumlah = parseInt(this.value || 0);

if(jumlah > stok){

Swal.fire({
icon:'warning',
title:'Stok Tidak Cukup',
text:'Jumlah melebihi stok obat!'
});

this.value = stok;

}

hitungTotal();

});


/* =========================
HITUNG TOTAL
========================= */

function hitungTotal(){

let harga = hargaInput.value.replace(/\D/g,'');
let jumlah = jumlahInput.value;

if(harga && jumlah){

let total = harga * jumlah;

totalInput.value = formatRupiah(total);

}

}


/* =========================
CEK SAAT SUBMIT
========================= */

formKeranjang.addEventListener('submit', function(e){

let stok = parseInt(stokInput.value || 0);
let exp = expInput.value;

let today = new Date();
today.setHours(0,0,0,0);

let expDate = new Date(exp);
expDate.setHours(0,0,0,0);

let diff = (expDate - today) / (1000*60*60*24);

/* stok habis */
if(stok === 0){

e.preventDefault();

Swal.fire({
icon:'error',
title:'Tidak Bisa Menambahkan',
text:'Stok obat habis'
});

return;

}

/* exp */
if(diff <= 30){

e.preventDefault();

Swal.fire({
icon:'error',
title:'Obat Tidak Bisa Dijual',
text:'Obat sudah expired atau mendekati expired (≤ 30 hari)'
});

return;

}

});

</script>