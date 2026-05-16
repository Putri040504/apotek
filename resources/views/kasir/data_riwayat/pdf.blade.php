<!DOCTYPE html>
<html>

<head>

    <title>Laporan Detail Penjualan</title>

    <style>
        body {
            font-family: Arial;
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

        .judul {
            text-align: center;
        }
    </style>

</head>

<body>

    <div class="judul">

        <h3>APOTEK ZEMA</h3>
        <h4>LAPORAN DETAIL PENJUALAN</h4>

    </div>

    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>Obat</th>
                <th>Expired</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>

        </thead>

        <tbody>

            @php
                $no = 1;
                $total = 0;
            @endphp

            @foreach ($riwayat as $p)
                @foreach ($p->detail as $d)
                    <tr>

                        <td>{{ $no++ }}</td>

                        <td>{{ $p->no_transaksi }}</td>

                        <td>{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}</td>

                        <td>{{ $d->obat?->nama_obat ?? '-' }}</td>

                        <td>
                            {{ $d->obat?->tanggal_exp ? date('d-m-Y', strtotime($d->obat->tanggal_exp)) : '-' }}
                        </td>

                        <td>Rp {{ number_format($d->harga, 0, ',', '.') }}</td>

                        <td>{{ $d->jumlah }}</td>

                        <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>

                    </tr>

                    @php
                        $total += $d->subtotal;
                    @endphp
                @endforeach
            @endforeach

        </tbody>

    </table>

    <br>

    <div style="text-align:right">

        <b>Total Penjualan : Rp {{ number_format($total, 0, ',', '.') }}</b>

    </div>

</body>

</html>
