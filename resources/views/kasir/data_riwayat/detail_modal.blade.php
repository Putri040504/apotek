<table class="table table-bordered table-sm">

<thead class="table-success">

<tr>
<th>Obat</th>
<th>Expired</th>
<th>Harga</th>
<th>Jumlah</th>
<th>Subtotal</th>
</tr>

</thead>

<tbody>

@foreach($penjualan->detail as $d)

<tr>

<td>{{ $d->obat->nama_obat }}</td>

<td>{{ date('d-m-Y', strtotime($d->obat->tanggal_exp)) }}</td>

<td>Rp {{ number_format($d->harga,0,',','.') }}</td>

<td>{{ $d->jumlah }}</td>

<td>Rp {{ number_format($d->subtotal,0,',','.') }}</td>

</tr>

@endforeach

</tbody>

</table>

<div class="text-end mt-3">

<b style="color:#198754;">
Total : Rp {{ number_format($penjualan->total,0,',','.') }}
</b>

</div>