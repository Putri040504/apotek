<div id="barcodePrintModal" class="barcode-print-modal" aria-hidden="true">
    <div class="barcode-print-backdrop" data-print-close></div>
    <div class="barcode-print-panel">
        <div class="barcode-print-header no-print">
            <h2><i class="bi bi-upc"></i> Cetak Label Barcode</h2>
            <button type="button" class="barcode-print-close" data-print-close aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="barcode-print-controls no-print">
            <label for="barcodePrintCopies">Jumlah label</label>
            <input type="number" id="barcodePrintCopies" value="1" min="1" max="50">
            <button type="button" id="btnBarcodePrint" class="btn-barcode-print">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
        <div id="barcodePrintPreview" class="barcode-print-preview"></div>
    </div>
</div>
