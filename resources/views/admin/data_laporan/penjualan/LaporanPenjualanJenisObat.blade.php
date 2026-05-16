<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penjualan Jenis Obat</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th {
            background: #eee;
        }

        th,
        td {
            padding: 6px;
            text-align: center;
        }
    </style>

</head>

<body>

    <h3 style="text-align:center">
        Laporan Penjualan Jenis Obat
    </h3>

    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>

        </thead>

        <tbody>

            @foreach ($data as $d)
                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $d->no_transaksi }}</td>

                    <td>{{ $d->tanggal }}</td>

                    <td>Rp {{ number_format($d->harga) }}</td>

                    <td>{{ $d->jumlah }}</td>

                    <td>Rp {{ number_format($d->total_penjualan) }}</td>

                </tr>
            @endforeach

        </tbody>

    </table>

    <br>

    <b>Total Penjualan : Rp {{ number_format($total) }}</b>

</body>

</html>
