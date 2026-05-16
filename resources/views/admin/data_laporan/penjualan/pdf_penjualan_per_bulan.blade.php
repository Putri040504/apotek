<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <title>Laporan Penjualan</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 6px;
            text-align: center;
        }

        th {
            background: #eaeaea;
        }

        .judul {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>

</head>

<body>

    <div class="judul">

        <h2>APOTEK ZEMA</h2>
        <h3>Laporan Penjualan Obat</h3>
        <p>Bulan {{ $bulan }} Tahun {{ $tahun }}</p>

    </div>


    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>No Transaksi</th>
                <th>Tanggal</th>
                <th>Jumlah Item</th>
                <th>Total</th>
            </tr>

        </thead>

        <tbody>

            @foreach ($data as $i => $d)
                <tr>

                    <td>{{ $i + 1 }}</td>
                    <td>{{ $d->no_transaksi }}</td>
                    <td>{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('d F Y') }}</td>
                    <td>{{ $d->jumlah_item }}</td>
                    <td>Rp {{ number_format($d->total, 0, ',', '.') }}</td>

                </tr>
            @endforeach

        </tbody>

    </table>

    <br>

    <h3>Total Penjualan : Rp {{ number_format($total, 0, ',', '.') }}</h3>

</body>

</html>
