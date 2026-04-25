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

/* CHECKBOX JADI HIJAU */

.form-check-input:checked{
background-color:#198754;
border-color:#198754;
}

.form-check-input:focus{
box-shadow:0 0 0 0.25rem rgba(25,135,84,0.25);
border-color:#198754;
}

/* INPUT FOCUS JADI HIJAU */

.form-control:focus{
border-color:#198754;
box-shadow:0 0 0 0.2rem rgba(25,135,84,0.25);
}

.form-check-input{
width: 32px;
height: 32px;
cursor: pointer;
}

/* WARNA HIJAU */

.form-check-input:checked{
background-color:#198754;
border-color:#198754;
}

/* Biar sejajar dengan tombol */

#tabelKeranjang .form-check-input{
margin-top:0;
}



</style>


<div class="modal fade" id="modalKeranjang">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<div class="modal-header bg-success text-white">

<h5 class="modal-title" style="font-size:16px;">
<i class="bi bi-cart3"></i> Keranjang Penjualan
</h5>

<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

</div>


<form id="formCheckout" action="{{ route('penjualan.checkout') }}" method="POST">
@csrf

<div class="modal-body" style="font-size:13px;">

<div class="table-responsive">

<table id="tabelKeranjang" class="table table-bordered text-center align-middle">

<thead class="table-success">
<tr>
<th width="50">No</th>
<th>Kode Keranjang</th>
<th>Kode Obat</th>
<th>Nama Obat</th>
<th>Jumlah</th>
<th>Total</th>
<th width="120">Pilih</th>
</tr>
</thead>

<tbody>

@php 
$no = 1;
$total = 0; 
@endphp

@forelse($keranjang as $k)

@php
$harga = $k->obat->harga_jual ?? 0;
$subtotal = $k->jumlah * $harga;
$total += $subtotal;
@endphp

<tr>

<td>{{ $no++ }}</td>

<td>KJ00{{ $k->id }}</td>

<td>{{ $k->obat->kode_obat ?? '-' }}</td>

<td class="text-start">{{ $k->obat->nama_obat ?? '-' }}</td>

<td>{{ $k->jumlah }}</td>

<td class="text-end">
Rp {{ number_format($subtotal,0,',','.') }}
</td>

<td>

<div class="d-flex justify-content-center gap-2">

<input 
type="checkbox"
name="keranjang_id[]"
value="{{ $k->id }}"
class="form-check-input checkItem"
data-subtotal="{{ $subtotal }}"
checked
>

<button type="button"
onclick="hapusKeranjang({{ $k->id }})"
class="btn btn-sm btn-outline-danger">

<i class="bi bi-trash"></i>

</button>

</div>

</td>

</tr>

@empty

<tr>
<td colspan="7" class="text-muted text-center">
Keranjang masih kosong
</td>
</tr>

@endforelse

</tbody>

</table>

</div>


<div class="row mt-3">

<div class="col-md-4">
<label>Total</label>
<div class="input-group">
<span class="input-group-text">Rp</span>
<input type="text" id="totalInput" class="form-control"
value="{{ number_format($total,0,',','.') }}" readonly>
</div>
</div>

<div class="col-md-4">
<label>Bayar</label>
<div class="input-group">
<span class="input-group-text">Rp</span>
<input type="text" name="bayar" id="bayar" class="form-control" required>
</div>
</div>

<div class="col-md-4">
<label>Kembali</label>
<div class="input-group">
<span class="input-group-text">Rp</span>
<input type="text" id="kembali" class="form-control" readonly>
</div>
</div>

</div>

<div class="d-flex justify-content-end mt-2">

<h5 class="text-success" style="font-size:16px;">
Total : <span id="totalSemua">Rp {{ number_format($total,0,',','.') }}</span>
</h5>

</div>

</div>


<div class="modal-footer">

<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
Tutup
</button>

<button type="submit" class="btn btn-success btn-sm">
<i class="bi bi-cart-check"></i> Checkout
</button>

</div>

</form>

</div>
</div>
</div>


<form id="formHapusKeranjang" method="POST">
@csrf
@method('DELETE')
</form>

<script>

function hapusKeranjang(id){
let form = document.getElementById('formHapusKeranjang');
form.action = "/kasir/keranjang/" + id;
form.submit();
}

function formatRupiah(angka){
return new Intl.NumberFormat('id-ID',{
style:'currency',
currency:'IDR',
minimumFractionDigits:0
}).format(angka);
}

function formatAngka(angka){
return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function ambilAngka(rp){
return parseInt(rp.toString().replace(/\./g,'')) || 0;
}

function hitungTotalKeranjang(){

let total = 0;

document.querySelectorAll('.checkItem:checked').forEach(function(item){

let subtotal = parseInt(item.getAttribute('data-subtotal')) || 0;
total += subtotal;

});

document.getElementById('totalSemua').innerText = formatRupiah(total);
document.getElementById('totalInput').value = formatAngka(total);

hitungKembalian();

}

function hitungKembalian(){

let total = ambilAngka(document.getElementById('totalInput').value);
let bayar = ambilAngka(document.getElementById('bayar').value);

let kembali = bayar - total;

if(kembali < 0){
kembali = 0;
}

document.getElementById('kembali').value = formatAngka(kembali);

}

document.getElementById('bayar').addEventListener('input', function(){

let angka = ambilAngka(this.value);

this.value = formatAngka(angka);

hitungKembalian();

});

document.querySelectorAll('.checkItem').forEach(function(item){

item.addEventListener('change', function(){
hitungTotalKeranjang();
});

});

document.getElementById('formCheckout').addEventListener('submit', function(e){

let total = ambilAngka(document.getElementById('totalInput').value);
let bayar = ambilAngka(document.getElementById('bayar').value);

if(bayar < total){

alert("Uang pembeli tidak cukup!");
e.preventDefault();

}

});

document.addEventListener("DOMContentLoaded", function(){

hitungTotalKeranjang();

});

</script>
