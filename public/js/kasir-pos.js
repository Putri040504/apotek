(function () {
    'use strict';

    const config = window.POS_CONFIG || {};
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    const el = {
        search: document.getElementById('posSearch'),
        dropdown: document.getElementById('posDropdown'),
        results: document.getElementById('posSearchResults'),
        cartWrap: document.getElementById('posCartWrap'),
        total: document.getElementById('posTotal'),
        itemCount: document.getElementById('posItemCount'),
        btnCash: document.getElementById('btnPayCash'),
        btnQris: document.getElementById('btnPayQris'),
        btnClear: document.getElementById('btnClearCart'),
        modalCash: document.getElementById('modalCash'),
        modalQris: document.getElementById('modalQris'),
        cashInput: document.getElementById('cashInput'),
        cashChange: document.getElementById('cashChange'),
        cashTotal: document.getElementById('cashTotal'),
        toast: document.getElementById('posToast'),
    };

    let searchTimer = null;
    let selectedObat = null;
    let cartTotal = parseInt(el.total?.dataset.value || '0', 10);
    let qrisPollTimer = null;
    let currentQrisId = null;

    function headers(json = true) {
        const h = {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
        };
        if (json) {
            h['Content-Type'] = 'application/json';
        }
        return h;
    }

    function showToast(message, type = 'success') {
        if (!el.toast) {
            return;
        }
        el.toast.textContent = message;
        el.toast.className = 'pos-toast show ' + type;
        setTimeout(() => el.toast.classList.remove('show'), 2500);
    }

    function formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function updateTotals(total, itemCount) {
        cartTotal = total;
        if (el.total) {
            el.total.textContent = formatRp(total);
            el.total.dataset.value = total;
        }
        if (el.itemCount) {
            el.itemCount.textContent = itemCount + ' item';
        }
        const disabled = total <= 0;
        if (el.btnCash) {
            el.btnCash.disabled = disabled;
        }
        if (el.btnQris) {
            el.btnQris.disabled = disabled;
        }
    }

    function focusSearch() {
        if (el.search) {
            el.search.focus();
            el.search.select();
        }
    }

    function beep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = 880;
            gain.gain.value = 0.05;
            osc.start();
            osc.stop(ctx.currentTime + 0.08);
        } catch (e) {
            // ignore
        }
    }

    async function api(url, options = {}) {
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            throw new Error(data.message || 'Terjadi kesalahan');
        }
        return data;
    }

    function renderItemHtml(o) {
        return (
            '<div class="pos-result-item" data-id="' +
            o.id +
            '">' +
            '<div class="info"><strong>' +
            escapeHtml(o.nama_obat) +
            '</strong><small>' +
            escapeHtml(o.kode_obat || '') +
            (o.barcode ? ' · EAN:' + escapeHtml(o.barcode) : '') +
            ' · Stok ' +
            o.stok +
            '</small></div>' +
            '<span class="price-tag">' +
            formatRp(o.harga_jual) +
            '</span></div>'
        );
    }

    function bindResultClicks(container, items) {
        container.querySelectorAll('.pos-result-item').forEach((node) => {
            node.addEventListener('click', () => {
                const obat = items.find((o) => String(o.id) === String(node.dataset.id));
                selectObat(obat);
                addToCart(obat.id, 1).catch((e) => showToast(e.message, 'error'));
            });
        });
    }

    function applyCartResponse(data) {
        if (el.cartWrap && data.html) {
            el.cartWrap.innerHTML = data.html;
            bindCartEvents();
        }
        updateTotals(data.total || 0, data.item_count || 0);
        if (data.message) {
            showToast(data.message, data.success === false ? 'error' : 'success');
        }
    }

    async function addToCart(obatId, jumlah) {
        const data = await api(config.routes.keranjangStore, {
            method: 'POST',
            headers: headers(),
            body: JSON.stringify({ obat_id: obatId, jumlah: jumlah }),
        });
        applyCartResponse(data);
        beep();
        el.search.value = '';
        selectedObat = null;
        if (el.dropdown) {
            el.dropdown.classList.remove('show');
        }
        focusSearch();
    }

    function bindCartEvents() {
        document.querySelectorAll('[data-cart-plus]').forEach((btn) => {
            btn.onclick = async () => {
                const id = btn.dataset.cartId;
                const qty = parseInt(btn.dataset.qty, 10) + 1;
                try {
                    const data = await api(config.routes.keranjangUpdate.replace('__ID__', id), {
                        method: 'PATCH',
                        headers: headers(),
                        body: JSON.stringify({ jumlah: qty }),
                    });
                    applyCartResponse(data);
                } catch (e) {
                    showToast(e.message, 'error');
                }
            };
        });

        document.querySelectorAll('[data-cart-minus]').forEach((btn) => {
            btn.onclick = async () => {
                const id = btn.dataset.cartId;
                const qty = parseInt(btn.dataset.qty, 10) - 1;
                if (qty < 1) {
                    return;
                }
                try {
                    const data = await api(config.routes.keranjangUpdate.replace('__ID__', id), {
                        method: 'PATCH',
                        headers: headers(),
                        body: JSON.stringify({ jumlah: qty }),
                    });
                    applyCartResponse(data);
                } catch (e) {
                    showToast(e.message, 'error');
                }
            };
        });

        document.querySelectorAll('[data-cart-remove]').forEach((btn) => {
            btn.onclick = async () => {
                const id = btn.dataset.cartId;
                try {
                    const data = await api(config.routes.keranjangDestroy.replace('__ID__', id), {
                        method: 'DELETE',
                        headers: headers(),
                    });
                    applyCartResponse(data);
                } catch (e) {
                    showToast(e.message, 'error');
                }
            };
        });

        document.querySelectorAll('[data-cart-qty]').forEach((input) => {
            const commitQty = async () => {
                const id = input.dataset.cartId;
                const prev = parseInt(input.dataset.qty, 10);
                const qty = parseInt(input.value, 10);

                if (!qty || qty < 1) {
                    input.value = prev;
                    return;
                }
                if (qty === prev) {
                    return;
                }

                try {
                    const data = await api(config.routes.keranjangUpdate.replace('__ID__', id), {
                        method: 'PATCH',
                        headers: headers(),
                        body: JSON.stringify({ jumlah: qty }),
                    });
                    applyCartResponse(data);
                } catch (e) {
                    input.value = prev;
                    showToast(e.message, 'error');
                }
            };

            input.addEventListener('change', () => {
                commitQty().catch((e) => showToast(e.message, 'error'));
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    input.blur();
                }
            });
        });
    }

    async function searchObat(q) {
        if (!q) {
            if (el.dropdown) {
                el.dropdown.classList.remove('show');
            }
            if (el.results) {
                el.results.innerHTML = '';
            }
            return;
        }

        const items = await api(config.routes.search + '?q=' + encodeURIComponent(q), {
            headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        });

        if (el.dropdown) {
            el.dropdown.innerHTML = items.map(renderItemHtml).join('');
            el.dropdown.classList.toggle('show', items.length > 0);
            bindResultClicks(el.dropdown, items);
        }

        if (el.results) {
            if (!items.length) {
                el.results.innerHTML = '<p class="text-muted p-3 mb-0">Tidak ada obat ditemukan</p>';
            } else {
                el.results.innerHTML = items.map(renderItemHtml).join('');
                bindResultClicks(el.results, items);
            }
        }
    }

    function selectObat(obat) {
        if (!obat) {
            return;
        }
        selectedObat = obat;
        el.search.value = obat.nama_obat;
        if (el.dropdown) {
            el.dropdown.classList.remove('show');
        }
    }

    async function scanByCode(kode) {
        const res = await fetch(config.routes.scan + '?kode=' + encodeURIComponent(kode), {
            headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        });
        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            throw new Error(data.error || data.message || 'Obat tidak ditemukan');
        }

        await addToCart(data.id, 1);
    }

    async function handleBarcodeEnter() {
        const value = el.search?.value.trim();
        if (!value) {
            return;
        }

        try {
            await scanByCode(value);
        } catch (e) {
            showToast(e.message || 'Kode tidak dikenali — pilih obat dari hasil cari', 'error');
        }
    }

    function openCameraScanner() {
        if (!window.BarcodeScanner) {
            showToast('Scanner kamera tidak tersedia', 'error');
            return;
        }

        BarcodeScanner.open({
            continuous: true,
            debounceMs: 2000,
            onDetected: async (code) => {
                try {
                    await scanByCode(code);
                } catch (e) {
                    showToast(e.message || 'Scan gagal', 'error');
                }
            },
        });
    }

    function openModal(modal) {
        modal?.classList.add('show');
    }

    function closeModal(modal) {
        modal?.classList.remove('show');
    }

    function openCashModal() {
        if (cartTotal <= 0) {
            return;
        }
        if (el.cashTotal) {
            el.cashTotal.textContent = formatRp(cartTotal);
        }
        if (el.cashInput) {
            el.cashInput.value = cartTotal;
            updateChange();
        }
        openModal(el.modalCash);
        setTimeout(() => el.cashInput?.focus(), 100);
    }

    function updateChange() {
        const bayar = parseInt(el.cashInput?.value || '0', 10);
        const kembali = Math.max(0, bayar - cartTotal);
        if (el.cashChange) {
            el.cashChange.textContent = formatRp(kembali);
        }
    }

    async function confirmCash() {
        const bayar = parseInt(el.cashInput?.value || '0', 10);
        if (bayar < cartTotal) {
            showToast('Uang tidak mencukupi', 'error');
            return;
        }

        const data = await api(config.routes.checkout, {
            method: 'POST',
            headers: headers(),
            body: JSON.stringify({ bayar }),
        });

        closeModal(el.modalCash);
        applyCartResponse(data);
        if (data.print_url) {
            window.open(data.print_url, '_blank', 'width=320,height=600');
        }
        showToast('Pembayaran berhasil!');
    }

    async function startQris() {
        if (cartTotal <= 0) {
            return;
        }

        const data = await api(config.routes.qris, {
            method: 'POST',
            headers: headers(),
            body: JSON.stringify({}),
        });

        currentQrisId = data.penjualan_id;
        if (el.modalQris) {
            el.modalQris.dataset.penjualanId = data.penjualan_id;
        }

        const qrContainer = document.getElementById('qrisContainer');
        if (qrContainer) {
            if (data.qr_string && data.qr_string.startsWith('http')) {
                qrContainer.innerHTML =
                    '<img src="' + data.qr_string + '" class="pos-qr-image" alt="QRIS">';
            } else if (data.qr_string) {
                qrContainer.innerHTML =
                    '<img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' +
                    encodeURIComponent(data.qr_string) +
                    '" class="pos-qr-image" alt="QRIS">';
            } else {
                qrContainer.innerHTML =
                    '<p class="text-muted">QRIS dibuat. Scan untuk membayar.</p>';
            }
        }

        const qrisTotal = document.getElementById('qrisTotal');
        if (qrisTotal) {
            qrisTotal.textContent = formatRp(data.total);
        }

        openModal(el.modalQris);
        pollQrisStatus(data.status_url);
    }

    function pollQrisStatus(statusUrl) {
        if (qrisPollTimer) {
            clearInterval(qrisPollTimer);
        }

        let attempts = 0;
        qrisPollTimer = setInterval(async () => {
            attempts++;
            if (attempts > 100) {
                clearInterval(qrisPollTimer);
                return;
            }

            try {
                const res = await fetch(statusUrl, {
                    headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                });
                const data = await res.json();

                if (data.status === 'paid') {
                    clearInterval(qrisPollTimer);
                    closeModal(el.modalQris);
                    showToast('Pembayaran QRIS berhasil!');
                    if (data.print_url) {
                        window.open(data.print_url, '_blank', 'width=320,height=600');
                    }
                    location.reload();
                } else if (data.status === 'cancelled') {
                    clearInterval(qrisPollTimer);
                    closeModal(el.modalQris);
                    showToast('Pembayaran dibatalkan', 'error');
                }
            } catch (e) {
                // keep polling
            }
        }, 3000);
    }

    async function cancelQris() {
        if (currentQrisId && config.routes.cancelQris) {
            try {
                await api(config.routes.cancelQris.replace('__ID__', currentQrisId), {
                    method: 'POST',
                    headers: headers(),
                    body: JSON.stringify({}),
                });
            } catch (e) {
                // ignore
            }
        }
        if (qrisPollTimer) {
            clearInterval(qrisPollTimer);
        }
        closeModal(el.modalQris);
    }

    document.querySelectorAll('.pos-quick-item').forEach((item) => {
        item.addEventListener('click', () => {
            addToCart(item.dataset.id, 1).catch((e) => showToast(e.message, 'error'));
        });
    });

    if (el.search) {
        el.search.addEventListener('input', () => {
            selectedObat = null;
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => searchObat(el.search.value.trim()), 250);
        });

        el.search.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleBarcodeEnter().catch((err) => showToast(err.message, 'error'));
            }
        });
    }

    document.getElementById('btnPosCamera')?.addEventListener('click', openCameraScanner);

    el.btnCash?.addEventListener('click', openCashModal);
    el.btnQris?.addEventListener('click', () => startQris().catch((e) => showToast(e.message, 'error')));

    el.btnClear?.addEventListener('click', async () => {
        if (!confirm('Kosongkan keranjang?')) {
            return;
        }
        try {
            const data = await api(config.routes.keranjangClear, {
                method: 'DELETE',
                headers: headers(),
            });
            applyCartResponse(data);
        } catch (e) {
            showToast(e.message, 'error');
        }
    });

    document.getElementById('btnConfirmCash')?.addEventListener('click', () => {
        confirmCash().catch((e) => showToast(e.message, 'error'));
    });
    document.getElementById('btnCloseCash')?.addEventListener('click', () => closeModal(el.modalCash));
    document.getElementById('btnCloseQris')?.addEventListener('click', cancelQris);

    el.cashInput?.addEventListener('input', updateChange);

    document.querySelectorAll('[data-quick-amount]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const type = btn.dataset.quickAmount;
            if (type === 'pas') {
                el.cashInput.value = cartTotal;
            } else {
                el.cashInput.value = parseInt(btn.dataset.value, 10);
            }
            updateChange();
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'F2') {
            e.preventDefault();
            focusSearch();
        }
        if (e.key === 'F3') {
            e.preventDefault();
            openCameraScanner();
        }
        if (e.key === 'F4' && !el.modalCash?.classList.contains('show')) {
            e.preventDefault();
            openCashModal();
        }
        if (e.key === 'F8' && !el.modalQris?.classList.contains('show')) {
            e.preventDefault();
            startQris().catch((err) => showToast(err.message, 'error'));
        }
        if (e.key === 'Escape') {
            closeModal(el.modalCash);
            closeModal(el.modalQris);
        }
    });

    bindCartEvents();
    focusSearch();
})();
