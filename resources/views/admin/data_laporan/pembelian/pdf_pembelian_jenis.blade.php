<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembelian Berdasarkan Jenis Obat</title>

    <style>

        body{
            font-family: sans-serif;
            font-size:12px;
        }

        h2{
            text-align:center;
            margin-bottom:5px;
        }

        h4{
            text-align:center;
            margin-top:0;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        table, th, td{
            border:1px solid black;
        }

        th{
            background:#f2f2f2;
        }

        th, td{
            padding:6px;
            text-align:center;
        }

    </style>
</head>

<body>

<h2>APOTEK ZEMA</h2>
<h4>Laporan Pembelian Berdasarkan Jenis Obat</h4>

<table>

<thead>
<tr>
<th>No</th>
<th>Kode Transaksi</th>
<th>Tanggal</th>
<th>Supplier</th>
<th>Harga Modal</th>
<th>Jumlah</th>
<th>Total</th>
</tr>
</thead>

<tbody>

@php $no = 1; @endphp

@foreach($data as $row)

<tr>
<td>{{ $no++ }}</td>
<td>{{ $row->kode_transaksi }}</td>
<td>{{ $row->tanggal_transaksi }}</td>
<td>{{ $row->supplier }}</td>
<td>{{ number_format($row->harga) }}</td>
<td>{{ $row->jumlah }}</td>
<td>{{ number_format($row->subtotal) }}</td>
</tr>

@endforeach

</tbody>

</table>

</body>
</html>