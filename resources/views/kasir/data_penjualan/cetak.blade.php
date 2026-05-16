<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan</title>
    <style>
        body {
            font-family: monospace;
            font-size: 13px;
            width: 280px;
            margin: auto;
        }

        .center {
            text-align: center;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 12px;
        }

        .item-name {
            flex: 1;
            word-break: break-word;
        }
    </style>
</head>

<body onload="window.print()">

    @php
        $bayar = $penjualan->bayar ?? 0;
        $kembali = $penjualan->kembalian ?? max(0, $bayar - $penjualan->total);
        $totalItem = 0;
        $metode = strtoupper($penjualan->metode_bayar ?? 'TUNAI');
    @endphp

    <div class="center">
        <div class="bold">APOTEK ZEMA</div>
        <div class="small">
            Jl. Contoh Alamat Apotek<br>
            Telp: 0812xxxxxxx
        </div>
    </div>

    <div class="line"></div>

    <div class="small">
        No : {{ $penjualan->no_transaksi }}<br>
        {{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y H:i') }}<br>
        Kasir : {{ $penjualan->user->name ?? auth()->user()->name }}<br>
        Bayar : {{ $metode }}
    </div>

    <div class="line"></div>

    <div class="row bold small">
        <span class="item-name">OBAT</span>
        <span>QTY</span>
        <span>SUB</span>
    </div>

    <div class="line"></div>

    @foreach ($penjualan->detail as $d)
        @php
            $totalItem += $d->jumlah;
            $namaObat = optional($d->obat)->nama_obat ?? 'Obat';
        @endphp
        <div class="row small">
            <span class="item-name">{{ \Illuminate\Support\Str::limit($namaObat, 18) }}</span>
            <span>{{ $d->jumlah }}</span>
            <span>{{ number_format($d->subtotal, 0, ',', '.') }}</span>
        </div>
    @endforeach

    <div class="line"></div>

    <div class="row small">
        <span>Item</span>
        <span>{{ $totalItem }}</span>
    </div>

    <div class="row bold">
        <span>TOTAL</span>
        <span>{{ number_format($penjualan->total, 0, ',', '.') }}</span>
    </div>

    @if ($metode === 'TUNAI')
        <div class="row small">
            <span>TUNAI</span>
            <span>{{ number_format($bayar, 0, ',', '.') }}</span>
        </div>
        <div class="row small">
            <span>KEMBALI</span>
            <span>{{ number_format($kembali, 0, ',', '.') }}</span>
        </div>
    @else
        <div class="row small">
            <span>QRIS</span>
            <span>LUNAS</span>
        </div>
    @endif

    <div class="line"></div>

    <div class="center small">
        TERIMA KASIH<br>
        SEMOGA LEKAS SEMBUH
    </div>

</body>

</html>
