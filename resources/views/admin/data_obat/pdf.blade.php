<h3 style="text-align:center">Data Obat</h3>

<table border="1" width="100%" cellspacing="0" cellpadding="5">

<tr>
<th>No</th>
<th>Kode</th>
<th>Nama Obat</th>
<th>Kategori</th>
<th>Stok</th>
<th>Harga Jual</th>
</tr>

@foreach($obat as $o)

<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $o->kode_obat }}</td>
<td>{{ $o->nama_obat }}</td>
<td>{{ $o->kategori->nama_kategori }}</td>
<td>{{ $o->stok }}</td>
<td>Rp {{ number_format($o->harga_jual,0,',','.') }}</td>
</tr>

@endforeach

</table>