<!DOCTYPE html>
<html>

<head>

    <title>Laporan Penjualan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body onload="window.print()">

    <div class="container mt-4">

        <h4 class="text-center">LAPORAN DETAIL PENJUALAN</h4>
        <h6 class="text-center">Apotek Zema</h6>

        <hr>

        <p>
            <b>Kode Transaksi :</b> {{ $penjualan->no_transaksi }} <br>
            <b>Tanggal :</b> {{ $penjualan->tanggal }}
        </p>

        <table class="table table-bordered">

            <thead>

                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Tanggal Exp</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>

            </thead>

            <tbody>

                @foreach ($penjualan->detail as $d)
                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $d->obat->nama_obat }}</td>

                        <td>
                            @php
                                $exp = $d->batchAllocations->first()?->stokBatch?->tanggal_exp
                                    ?? $d->obat?->earliestExpiryBatch()?->tanggal_exp;
                            @endphp
                            {{ $exp?->format('d-m-Y') ?? '-' }}
                        </td>

                        <td>Rp {{ number_format($d->harga) }}</td>

                        <td>{{ $d->jumlah }}</td>

                        <td>Rp {{ number_format($d->subtotal) }}</td>

                    </tr>
                @endforeach

            </tbody>

        </table>

        <h5 class="text-end">

            Total : Rp {{ number_format($penjualan->total) }}

        </h5>

    </div>

</body>

</html>
