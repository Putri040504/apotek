/**
 * Barcode label print (JsBarcode CODE128).
 * Usage: BarcodePrint.show({ kode: 'OB001', nama: 'Paracetamol' })
 */
(function () {
    'use strict';

    if (typeof JsBarcode === 'undefined') {
        console.warn('JsBarcode library not loaded');
        return;
    }

    const modal = document.getElementById('barcodePrintModal');
    const preview = document.getElementById('barcodePrintPreview');
    const copiesInput = document.getElementById('barcodePrintCopies');
    const printBtn = document.getElementById('btnBarcodePrint');

    if (!modal || !preview) {
        return;
    }

    let current = { kode: '', nama: '', kodeInternal: '' };

    function barcodeFormat(code) {
        if (/^\d{13}$/.test(code)) {
            return 'EAN13';
        }
        if (/^\d{8}$/.test(code)) {
            return 'EAN8';
        }
        return 'CODE128';
    }

    function truncate(str, max) {
        if (!str || str.length <= max) {
            return str || '';
        }
        return str.slice(0, max - 1) + '…';
    }

    function buildLabel(kode, nama, kodeInternal) {
        const wrap = document.createElement('div');
        wrap.className = 'barcode-label';

        const nameEl = document.createElement('p');
        nameEl.className = 'barcode-label-name';
        nameEl.textContent = truncate(nama, 40);
        wrap.appendChild(nameEl);

        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('class', 'barcode-svg');
        wrap.appendChild(svg);

        const format = barcodeFormat(kode);

        try {
            JsBarcode(svg, kode, {
                format: format,
                width: 1.5,
                height: 50,
                displayValue: false,
                margin: 4,
            });
        } catch (e) {
            try {
                JsBarcode(svg, kode, {
                    format: 'CODE128',
                    width: 1.5,
                    height: 50,
                    displayValue: false,
                    margin: 4,
                });
            } catch (e2) {
                const err = document.createElement('p');
                err.textContent = 'Kode tidak valid untuk barcode';
                err.style.color = '#dc2626';
                wrap.appendChild(err);
                return wrap;
            }
        }

        const codeEl = document.createElement('p');
        codeEl.className = 'barcode-label-code';
        codeEl.textContent = kode;
        wrap.appendChild(codeEl);

        if (kodeInternal && kodeInternal !== kode) {
            const internalEl = document.createElement('p');
            internalEl.className = 'barcode-label-internal';
            internalEl.textContent = 'Kode internal: ' + kodeInternal;
            wrap.appendChild(internalEl);
        }

        return wrap;
    }

    function renderPreview() {
        preview.innerHTML = '';
        const copies = Math.min(50, Math.max(1, parseInt(copiesInput?.value || '1', 10) || 1));

        for (let i = 0; i < copies; i++) {
            preview.appendChild(buildLabel(current.kode, current.nama, current.kodeInternal));
        }
    }

    const BarcodePrint = {
        show(opts) {
            current = {
                kode: String(opts?.kode || '').trim(),
                nama: String(opts?.nama || '').trim(),
                kodeInternal: String(opts?.kodeInternal || '').trim(),
            };

            if (!current.kode) {
                return;
            }

            if (copiesInput) {
                copiesInput.value = opts?.copies ?? 1;
            }

            renderPreview();
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
        },

        close() {
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
        },
    };

    modal.querySelectorAll('[data-print-close]').forEach((el) => {
        el.addEventListener('click', () => BarcodePrint.close());
    });

    if (copiesInput) {
        copiesInput.addEventListener('change', renderPreview);
        copiesInput.addEventListener('input', renderPreview);
    }

    if (printBtn) {
        printBtn.addEventListener('click', () => {
            renderPreview();
            window.print();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            BarcodePrint.close();
        }
    });

    window.BarcodePrint = BarcodePrint;
})();
