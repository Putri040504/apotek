@extends('kasir.layout.pos')

@php
    $cartTotal = $keranjang->sum(fn($i) => ($i->obat->harga_jual ?? 0) * $i->jumlah);
    $cartCount = $keranjang->sum('jumlah');
@endphp

@section('content')
    <div class="pos-app">
        <header class="pos-header">
            <div class="pos-header-brand">
                <img src="{{ asset('logo/apotek zema.png') }}" alt="Apotek Zema">
                <h1>APOTEK ZEMA — KASIR</h1>
            </div>
            <div class="pos-header-meta">
                <span><i class="bi bi-person"></i> {{ Auth::user()->name }}</span>
                <span><i class="bi bi-clock"></i> <span id="posClock">{{ now()->format('d/m/Y H:i') }}</span></span>
            </div>
            <div class="pos-header-actions">
                <a href="{{ route('kasir.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="{{ route('riwayat.penjualan') }}"><i class="bi bi-receipt"></i> Riwayat</a>
                <a href="{{ route('kasir.profile') }}"><i class="bi bi-person"></i> Profil</a>
            </div>
        </header>

        <div class="pos-scan-bar">
            <div class="pos-scan-wrap">
                <label for="posSearch">
                    SCAN / CARI OBAT
                    <span class="pos-kbd-hint">Enter · F2 focus · F3 kamera</span>
                </label>
                <div class="pos-scan-input-row">
                    <input type="text" id="posSearch" class="pos-scan-input" placeholder="Kode barcode atau nama obat..."
                        autocomplete="off" autofocus>
                    <button type="button" id="btnPosCamera" class="pos-btn-camera" title="Scan kamera (F3)">
                        <i class="bi bi-camera"></i>
                    </button>
                </div>
                <div id="posDropdown" class="pos-search-dropdown"></div>
            </div>
            <div class="pos-qty-wrap">
                <label for="posQty">Qty</label>
                <input type="number" id="posQty" value="1" min="1">
            </div>
            <button type="button" id="btnAddItem" class="pos-btn-add">
                <i class="bi bi-plus-lg"></i> Tambah
            </button>
        </div>

        <div class="pos-main">
            <div class="pos-left">
                @if ($topObat->isNotEmpty())
                    <div class="pos-quick-grid">
                        <p class="pos-quick-title">Obat terlaris — klik untuk tambah</p>
                        @foreach ($topObat as $obat)
                            @if ($obat && $obat->stok > 0)
                                <button type="button" class="pos-quick-item" data-id="{{ $obat->id }}">
                                    <div class="name">{{ Str::limit($obat->nama_obat, 28) }}</div>
                                    <div class="price">Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}</div>
                                    <div class="stock">Stok: {{ $obat->stok }}</div>
                                </button>
                            @endif
                        @endforeach
                    </div>
                @endif
                <div id="posSearchResults" class="pos-search-results"></div>
            </div>

            <aside class="pos-right">
                <div class="pos-cart-header">
                    <span><i class="bi bi-cart3"></i> Keranjang</span>
                    <span id="posItemCount">{{ $cartCount }} item</span>
                </div>
                <div id="posCartWrap" class="pos-cart-items">
                    @include('kasir.pos._cart', ['keranjang' => $keranjang])
                </div>
                <div class="pos-cart-footer">
                    <div class="pos-total-row">
                        <span class="label">TOTAL</span>
                        <span id="posTotal" class="amount" data-value="{{ $cartTotal }}">Rp
                            {{ number_format($cartTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="pos-cart-actions">
                        <button type="button" id="btnClearCart" class="pos-btn pos-btn-cancel">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="button" id="btnPayCash" class="pos-btn pos-btn-cash" @disabled($cartTotal <= 0)>
                            <i class="bi bi-cash-stack"></i> Tunai (F4)
                        </button>
                        <button type="button" id="btnPayQris" class="pos-btn pos-btn-qris" @disabled($cartTotal <= 0)>
                            <i class="bi bi-qr-code"></i> QRIS (F8)
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <div id="modalCash" class="pos-modal-overlay">
        <div class="pos-modal">
            <h3><i class="bi bi-cash-stack"></i> Bayar Tunai</h3>
            <p class="text-muted mb-0">Total belanja</p>
            <div id="cashTotal" class="pos-cash-display">Rp 0</div>
            <label class="form-label fw-semibold">Uang diterima</label>
            <input type="number" id="cashInput" class="pos-cash-input" min="0" step="1000">
            <div class="pos-quick-amounts">
                <button type="button" data-quick-amount="pas">Pas</button>
                <button type="button" data-quick-amount="amount" data-value="50000">50rb</button>
                <button type="button" data-quick-amount="amount" data-value="100000">100rb</button>
                <button type="button" data-quick-amount="amount" data-value="20000">20rb</button>
                <button type="button" data-quick-amount="amount" data-value="10000">10rb</button>
                <button type="button" data-quick-amount="amount" data-value="5000">5rb</button>
            </div>
            <div class="pos-change-display">
                <div class="label">Kembalian</div>
                <div id="cashChange" class="value">Rp 0</div>
            </div>
            <div class="pos-modal-actions">
                <button type="button" id="btnCloseCash" class="pos-btn pos-btn-cancel">Batal</button>
                <button type="button" id="btnConfirmCash" class="pos-btn pos-btn-cash">Bayar</button>
            </div>
        </div>
    </div>

    <div id="modalQris" class="pos-modal-overlay" data-penjualan-id="">
        <div class="pos-modal pos-modal-lg">
            <h3><i class="bi bi-qr-code"></i> Bayar QRIS</h3>
            <p class="text-muted">Scan QR dengan e-wallet / m-banking</p>
            <div id="qrisTotal" class="pos-cash-display mb-2">Rp 0</div>
            <div id="qrisContainer"></div>
            <p class="small text-muted mt-2">Menunggu pembayaran...</p>
            <div class="pos-modal-actions mt-3">
                <button type="button" id="btnCloseQris" class="pos-btn pos-btn-cancel w-100">Batalkan</button>
            </div>
        </div>
    </div>

    <div id="posToast" class="pos-toast"></div>

    <script>
        window.POS_CONFIG = {
            routes: {
                search: @json(route('kasir.obat.search')),
                scan: @json(route('kasir.obat.scan')),
                keranjangStore: @json(route('keranjang.store')),
                keranjangUpdate: @json(url('/kasir/keranjang/__ID__')),
                keranjangDestroy: @json(url('/kasir/keranjang/__ID__')),
                keranjangClear: @json(route('keranjang.clear')),
                checkout: @json(route('penjualan.checkout')),
                qris: @json(route('penjualan.qris')),
                cancelQris: @json(url('/kasir/penjualan/__ID__/cancel-qris')),
            }
        };

        setInterval(() => {
            const el = document.getElementById('posClock');
            if (el) {
                const d = new Date();
                el.textContent = d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }, 30000);
    </script>
@endsection
