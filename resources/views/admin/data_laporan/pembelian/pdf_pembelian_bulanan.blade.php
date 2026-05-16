<h3 style="text-align:center">
    Laporan Pembelian Obat
</h3>

<p>Bulan : {{ $bulan }}</p>

<table border="1" width="100%" cellpadding="5" cellspacing="0">

    <tr>
        <th>No</th>
        <th>No Transaksi</th>
        <th>Tanggal</th>
        <th>Supplier</th>
        <th>Jumlah Item</th>
        <th>Total</th>
    </tr>

    @foreach ($data as $d)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $d->kode_transaksi }}</td>
            <td>{{ $d->tanggal_transaksi }}</td>
            <td>{{ $d->supplier }}</td>
            <td>{{ $d->jumlah_item }}</td>
            <td>Rp {{ number_format($d->total_harga, 0, ',', '.') }}</td>
        </tr>
    @endforeach

</table>

<br>

<b>Total Pembelian : Rp {{ number_format($total, 0, ',', '.') }}</b>
