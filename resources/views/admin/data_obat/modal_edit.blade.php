<div class="modal fade" id="modalEdit{{ $o->id }}" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('obat.update', $o->id) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" style="font-size:16px;">
                        <i class="bi bi-pencil-square me-2"></i> Edit Data Obat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>


                <div class="modal-body" style="font-size:14px;">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Obat</label>
                            <input type="text" name="kode_obat" value="{{ $o->kode_obat }}"
                                class="form-control bg-light" readonly style="cursor:not-allowed">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barcode Kemasan (EAN)</label>
                            <div class="input-group">
                                <input type="text" name="barcode" id="editBarcode{{ $o->id }}"
                                    class="form-control" value="{{ $o->barcode }}" placeholder="8991234567890"
                                    maxlength="50" inputmode="numeric" autocomplete="off">
                                <button type="button" class="btn btn-outline-primary btn-scan-barcode-edit"
                                    data-target="#editBarcode{{ $o->id }}" title="Scan dari kemasan">
                                    <i class="bi bi-camera"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Kosongkan jika tidak pakai barcode kemasan.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Obat</label>
                            <input type="text" name="nama_obat" value="{{ $o->nama_obat }}" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>

                            <select name="kategori_id" class="form-control">

                                @foreach ($kategori as $k)
                                    <option value="{{ $k->id }}"
                                        {{ $o->kategori_id == $k->id ? 'selected' : '' }}>

                                        {{ $k->nama_kategori }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Exp</label>
                            <input type="date" name="tanggal_exp" value="{{ $o->tanggal_exp }}"
                                class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" value="{{ $o->stok }}" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Beli</label>
                            <input type="text" name="harga_beli" value="{{ $o->harga_beli }}"
                                class="form-control rupiah">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Jual</label>
                            <input type="text" name="harga_jual" value="{{ $o->harga_jual }}"
                                class="form-control rupiah">
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-success btn-sm">
                        <i class="bi bi-save"></i> Update
                    </button>

                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        Batal
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
