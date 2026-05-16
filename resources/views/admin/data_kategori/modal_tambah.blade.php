<div class="modal fade" id="modalTambah">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form action="{{ route('kategori.store') }}" method="POST" autocomplete="off">
                @csrf

                <div class="modal-header bg-success text-white">

                    <h5 class="modal-title" style="font-size:16px;">
                        <i class="bi bi-tags me-2"></i> Tambah Data Kategori
                    </h5>

                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body" style="font-size:14px;">

                    <div class="mb-3">
                        <label class="form-label">Kode Kategori</label>

                        <input type="text" class="form-control bg-light" value="Otomatis dari sistem" disabled
                            style="cursor:not-allowed;">

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>

                        <input type="text" name="nama_kategori" class="form-control" autocomplete="off"
                            spellcheck="false" autocorrect="off" autocapitalize="off" required>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit" class="btn btn-success btn-sm">
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
