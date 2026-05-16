/**
 * Shared camera barcode scanner (html5-qrcode).
 * Usage: BarcodeScanner.open({ onDetected, continuous, debounceMs })
 */
(function () {
    'use strict';

    if (typeof Html5Qrcode === 'undefined') {
        console.warn('Html5Qrcode library not loaded');
        return;
    }

    const modal = document.getElementById('barcodeScannerModal');
    const readerEl = document.getElementById('barcode-reader');
    const statusEl = document.getElementById('barcodeScannerStatus');
    const flipBtn = document.getElementById('btnBarcodeFlip');

    if (!modal || !readerEl) {
        return;
    }

    let scanner = null;
    let isRunning = false;
    let facingMode = 'environment';
    let lastCode = '';
    let lastScanAt = 0;
    let options = {};

    const formats = [
        Html5QrcodeSupportedFormats.CODE_128,
        Html5QrcodeSupportedFormats.CODE_39,
        Html5QrcodeSupportedFormats.EAN_13,
        Html5QrcodeSupportedFormats.EAN_8,
        Html5QrcodeSupportedFormats.UPC_A,
        Html5QrcodeSupportedFormats.QR_CODE,
    ];

    function setStatus(text, type) {
        if (!statusEl) {
            return;
        }
        statusEl.textContent = text;
        statusEl.className = 'barcode-scanner-status' + (type ? ' ' + type : '');
    }

    function playBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = 880;
            gain.gain.value = 0.08;
            osc.start();
            osc.stop(ctx.currentTime + 0.08);
        } catch (e) {
            /* ignore */
        }
    }

    function shouldIgnore(code) {
        const debounce = options.debounceMs ?? 2000;
        const now = Date.now();
        if (code === lastCode && now - lastScanAt < debounce) {
            return true;
        }
        lastCode = code;
        lastScanAt = now;
        return false;
    }

    async function onScanSuccess(decodedText) {
        const code = String(decodedText || '').trim();
        if (!code || shouldIgnore(code)) {
            return;
        }

        setStatus('Terbaca: ' + code, 'success');

        if (typeof options.onDetected === 'function') {
            const result = options.onDetected(code);
            if (result && typeof result.then === 'function') {
                await result;
            }
        }

        playBeep();

        if (!options.continuous) {
            BarcodeScanner.close();
        }
    }

    function onScanFailure() {
        /* ignore frame failures */
    }

    async function startCamera() {
        if (!scanner) {
            scanner = new Html5Qrcode('barcode-reader');
        }

        if (isRunning) {
            await stopCamera();
        }

        setStatus('Memuat kamera...', '');

        const config = {
            fps: 10,
            qrbox: { width: 280, height: 160 },
            formatsToSupport: formats,
        };

        try {
            await scanner.start({ facingMode }, config, onScanSuccess, onScanFailure);
            isRunning = true;
            setStatus('Siap scan — arahkan ke barcode', '');
        } catch (err) {
            isRunning = false;
            const msg =
                err && err.name === 'NotAllowedError'
                    ? 'Izin kamera ditolak. Aktifkan di pengaturan browser atau gunakan HTTPS/localhost.'
                    : 'Gagal membuka kamera: ' + (err.message || 'perangkat tidak tersedia');
            setStatus(msg, 'error');
        }
    }

    async function stopCamera() {
        if (scanner && isRunning) {
            try {
                await scanner.stop();
            } catch (e) {
                /* ignore */
            }
            isRunning = false;
        }
    }

    const BarcodeScanner = {
        open(opts) {
            options = opts || {};
            lastCode = '';
            lastScanAt = 0;
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            startCamera();
        },

        async close() {
            await stopCamera();
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            setStatus('Memuat kamera...', '');
        },

        isOpen() {
            return modal.classList.contains('show');
        },
    };

    modal.querySelectorAll('[data-barcode-close]').forEach((el) => {
        el.addEventListener('click', () => BarcodeScanner.close());
    });

    if (flipBtn) {
        flipBtn.addEventListener('click', () => {
            facingMode = facingMode === 'environment' ? 'user' : 'environment';
            startCamera();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && BarcodeScanner.isOpen()) {
            BarcodeScanner.close();
        }
    });

    window.BarcodeScanner = BarcodeScanner;
})();
