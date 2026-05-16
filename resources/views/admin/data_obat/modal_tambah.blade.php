<div class="modal fade" id="modalTambah" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('obat.store') }}" method="POST">

                @csrf

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" style="font-size:16px;">
                        <i class="bi bi-capsule me-2"></i> Tambah Data Obat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="font-size:14px;">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Obat</label>
                            <input type="text" name="kode_obat" id="tambahKodeObat"
                                value="{{ 'OB' . str_pad($obat->count() + 1, 3, '0', STR_PAD_LEFT) }}"
                                class="form-control bg-light" readonly style="cursor:not-allowed">
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-1"
                                id="btnPreviewBarcodeTambah">
                                <i class="bi bi-upc"></i> Preview / Cetak Barcode
                            </button>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barcode Kemasan (EAN)</label>
                            <div class="input-group">
                                <input type="text" name="barcode" id="tambahBarcode" class="form-control"
                                    value="{{ old('barcode') }}" placeholder="8991234567890" maxlength="50"
                                    inputmode="numeric" autocomplete="off">
                                <button type="button" class="btn btn-outline-primary" id="btnScanBarcodeTambah"
                                    title="Scan dari kemasan">
                                    <i class="bi bi-camera"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Opsional — isi angka di kemasan untuk scan
                                POS/pembelian.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Obat</label>
                            <input type="text" name="nama_obat" value="{{ old('nama_obat') }}" class="form-control"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>

                            <select name="kategori_id" class="form-control">

                                <option value="">-- Pilih Kategori --</option>

                                @foreach ($kategori as $k)
                                    <option value="{{ $k->id }}"
                                        {{ old('kategori_id') == $k->id ? 'selected' : '' }}>

                                        {{ $k->nama_kategori }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Exp</label>
                            <input type="date" name="tanggal_exp" value="{{ old('tanggal_exp') }}"
                                class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" value="{{ old('stok') }}" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Beli</label>
                            <input type="text" name="harga_beli" value="{{ old('harga_beli') }}"
                                class="form-control rupiah">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Jual</label>
                            <input type="text" name="harga_jual" value="{{ old('harga_jual') }}"
                                class="form-control rupiah">
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-success btn-sm">
                        <i class="bi bi-save"></i> Simpan
                    </button>

                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        Batal
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
