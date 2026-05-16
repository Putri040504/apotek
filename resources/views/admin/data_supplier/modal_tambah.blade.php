<div class="modal fade" id="modalTambah" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('supplier.store') }}" method="POST">

                @csrf

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" style="font-size:16px;">
                        <i class="bi bi-plus-circle me-2"></i> Tambah Supplier
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="font-size:14px;">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Supplier</label>
                            <input type="text" name="kode_supplier" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Supplier</label>
                            <input type="text" name="nama_supplier" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Obat</label>

                            <select name="obat_id" class="form-control">

                                @foreach ($obat as $o)
                                    <option value="{{ $o->id }}">
                                        {{ $o->nama_obat }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">No Telp</label>
                            <input type="text" name="no_telp" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alamat</label>
                            <input type="text" name="alamat" class="form-control">
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
