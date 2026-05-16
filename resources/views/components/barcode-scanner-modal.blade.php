<div id="barcodeScannerModal" class="barcode-scanner-modal" aria-hidden="true">
    <div class="barcode-scanner-backdrop" data-barcode-close></div>
    <div class="barcode-scanner-panel">
        <div class="barcode-scanner-header">
            <h2><i class="bi bi-camera"></i> Scan Barcode</h2>
            <button type="button" class="barcode-scanner-close" data-barcode-close aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <p class="barcode-scanner-hint">Arahkan kamera ke barcode. Pastikan pencahayaan cukup.</p>
        <div id="barcode-reader" class="barcode-reader-wrap"></div>
        <p id="barcodeScannerStatus" class="barcode-scanner-status">Memuat kamera...</p>
        <div class="barcode-scanner-actions">
            <button type="button" id="btnBarcodeFlip" class="btn-barcode-flip">
                <i class="bi bi-arrow-repeat"></i> Ganti Kamera
            </button>
        </div>
    </div>
</div>
