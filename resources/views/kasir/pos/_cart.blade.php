@if($keranjang->isEmpty())
    <div class="pos-cart-empty">
        <i class="bi bi-cart3" style="font-size:2rem;opacity:0.3"></i>
        <p class="mt-2 mb-0">Keranjang kosong</p>
        <small>Scan atau cari obat untuk mulai</small>
    </div>
@else
    @foreach($keranjang as $item)
        @php
            $harga = $item->obat->harga_jual ?? 0;
            $sub = $harga * $item->jumlah;
        @endphp
        <div class="pos-cart-line" data-cart-line="{{ $item->id }}">
            <div class="detail">
                <div class="name">{{ $item->obat->nama_obat ?? '-' }}</div>
                <div class="unit-price">{{ number_format($harga, 0, ',', '.') }} × {{ $item->jumlah }}</div>
            </div>
            <div class="qty-control">
                <button type="button" data-cart-minus data-cart-id="{{ $item->id }}" data-qty="{{ $item->jumlah }}">−</button>
                <span>{{ $item->jumlah }}</span>
                <button type="button" data-cart-plus data-cart-id="{{ $item->id }}" data-qty="{{ $item->jumlah }}">+</button>
            </div>
            <div class="subtotal">Rp {{ number_format($sub, 0, ',', '.') }}</div>
            <button type="button" class="btn-remove" data-cart-remove data-cart-id="{{ $item->id }}" title="Hapus">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    @endforeach
@endif
