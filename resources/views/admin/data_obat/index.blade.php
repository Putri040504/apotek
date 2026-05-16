@extends('admin.layout.app')

@section('title')
    Data Obat
@endsection

@section('content')
    <style>
        #tabelObat {
            font-size: 13px;
        }

        #tabelObat th {
            font-size: 12px;
            padding: 6px;
        }

        #tabelObat td {
            padding: 5px;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Data
            </button>

            <button type="button" class="btn btn-primary me-2" id="btnObatScanCari">
                <i class="bi bi-camera"></i> Scan Cari Obat
            </button>

        </div>

        <div>

            <a href="{{ route('obat.excel') }}" class="btn btn-outline-success me-2">
                <i class="bi bi-file-earmark-excel-fill"></i> Excel
            </a>

            <a href="{{ route('obat.pdf') }}" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF
            </a>

        </div>

    </div>


    <div class="card">
        <div class="card-body">

            <table id="tabelObat" class="table table-bordered text-center align-middle">

                <thead class="header-hijau">

                    <tr>

                        <th width="60" class="text-center align-middle">No</th>
                        <th class="text-center align-middle">Kode Obat</th>
                        <th class="text-center align-middle">Barcode Kemasan</th>
                        <th class="text-center align-middle">Nama Obat</th>
                        <th class="text-center align-middle">Tanggal EXP</th>
                        <th class="text-center align-middle">Kategori</th>
                        <th class="text-center align-middle">Stok</th>
                        <th class="text-center align-middle">Harga Beli</th>
                        <th class="text-center align-middle">Harga Jual</th>
                        <th width="160" class="text-center align-middle">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach ($obat as $o)
                        <tr data-kode="{{ $o->kode_obat }}" data-barcode="{{ $o->barcode }}"
                            data-nama="{{ $o->nama_obat }}">

                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $o->kode_obat }}</td>

                            <td>
                                @if ($o->barcode)
                                    <code>{{ $o->barcode }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>{{ $o->nama_obat }}</td>

                            <td>
                                {{ $o->tanggal_exp?->format('d-m-Y') ?? '-' }}
                                @if ($o->stokBatches->where('jumlah', '>', 0)->count() > 1)
                                    <small
                                        class="text-muted d-block">{{ $o->stokBatches->where('jumlah', '>', 0)->count() }}
                                        batch</small>
                                @endif
                            </td>

                            <td>{{ $o->kategori->nama_kategori ?? '-' }}</td>

                            <td>
                                <strong>{{ $o->stok }}</strong>
                                <small class="text-muted d-block">sellable: {{ $o->sellableStock() }}</small>
                            </td>

                            <td>Rp {{ number_format($o->harga_beli, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($o->harga_jual, 0, ',', '.') }}</td>

                            <td>

                                <button type="button" class="btn btn-sm btn-outline-secondary btn-cetak-barcode"
                                    data-scan-code="{{ $o->scanCodeForLabel() }}" data-kode-internal="{{ $o->kode_obat }}"
                                    data-nama="{{ $o->nama_obat }}" title="Cetak label barcode">

                                    <i class="bi bi-upc"></i>

                                </button>

                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                    data-bs-target="#modalEdit{{ $o->id }}">

                                    <i class="bi bi-pencil-square"></i>

                                </button>


                                <form id="delete-form-{{ $o->id }}" action="{{ route('obat.destroy', $o->id) }}"
                                    method="POST" style="display:inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $o->id }})">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                        @include('admin.data_obat.modal_edit')
                    @endforeach

                </tbody>

            </table>

        </div>
    </div>

    @include('admin.data_obat.modal_tambah')


    @push('scripts')
        <script>
            const adminObatLookupUrl = @json(route('admin.obat.lookup'));
            let tabelObatDt;

            $(document).ready(function() {

                tabelObatDt = $('#tabelObat').DataTable({

                    pageLength: 5,

                    lengthMenu: [
                        [5, 10, 25, 50],
                        [5, 10, 25, 50]
                    ],

                    language: {
                        search: "Search:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        zeroRecords: "Data tidak ditemukan",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Tidak ada data",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        paginate: {
                            previous: "Sebelumnya",
                            next: "Berikutnya"
                        }
                    }

                });

            });

            function highlightObatRow(kode, barcode) {
                $('#tabelObat tbody tr').removeClass('table-warning');
                let row = $('#tabelObat tbody tr[data-kode="' + kode + '"]');
                if (!row.length && barcode) {
                    row = $('#tabelObat tbody tr[data-barcode="' + barcode + '"]');
                }
                if (row.length) {
                    row.addClass('table-warning');
                    row[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            $(document).on('click', '.btn-cetak-barcode', function() {
                if (!window.BarcodePrint) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fitur cetak tidak tersedia'
                    });
                    return;
                }
                BarcodePrint.show({
                    kode: $(this).data('scanCode'),
                    kodeInternal: $(this).data('kodeInternal'),
                    nama: $(this).data('nama'),
                });
            });

            $('#btnPreviewBarcodeTambah').on('click', function() {
                const scanCode = ($('#tambahBarcode').val() || '').trim() || $('#tambahKodeObat').val();
                const nama = $('input[name="nama_obat"]').val() || scanCode;
                if (!scanCode || !window.BarcodePrint) {
                    return;
                }
                BarcodePrint.show({
                    kode: scanCode,
                    kodeInternal: $('#tambahKodeObat').val(),
                    nama: nama,
                });
            });

            function openScanToInput(inputSelector) {
                if (!window.BarcodeScanner) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Scanner tidak tersedia'
                    });
                    return;
                }
                BarcodeScanner.open({
                    continuous: false,
                    debounceMs: 1200,
                    onDetected: function(code) {
                        const digits = code.replace(/\D/g, '');
                        $(inputSelector).val(digits.length >= 8 ? digits : code.trim());
                    },
                });
            }

            $('#btnScanBarcodeTambah').on('click', function() {
                openScanToInput('#tambahBarcode');
            });

            $(document).on('click', '.btn-scan-barcode-edit', function() {
                openScanToInput($(this).data('target'));
            });

            $('#btnObatScanCari').on('click', function() {
                if (!window.BarcodeScanner) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Scanner tidak tersedia'
                    });
                    return;
                }

                BarcodeScanner.open({
                    continuous: false,
                    debounceMs: 1500,
                    onDetected: async function(code) {
                        try {
                            const res = await fetch(adminObatLookupUrl + '?kode=' + encodeURIComponent(
                                code), {
                                    headers: {
                                        Accept: 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                });
                            const data = await res.json();
                            if (!res.ok) {
                                throw new Error(data.error || 'Obat tidak ditemukan');
                            }

                            const searchTerm = [data.kode_obat, data.barcode].filter(Boolean).join(' ');
                            tabelObatDt.search(searchTerm).draw();
                            highlightObatRow(data.kode_obat, data.barcode);

                            const detail = data.barcode ?
                                'Internal: ' + data.kode_obat + ' · EAN: ' + data.barcode :
                                'Kode: ' + data.kode_obat;

                            Swal.fire({
                                icon: 'success',
                                title: data.nama_obat,
                                text: detail,
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        } catch (err) {
                            Swal.fire({
                                icon: 'error',
                                title: err.message || 'Scan gagal'
                            });
                        }
                    },
                });
            });


            @if (session('success'))

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    timer: 2000,
                    showConfirmButton: false
                })
            @endif


            @if ($errors->any())

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ $errors->first() }}'
                })
            @endif


            function confirmDelete(id) {

                Swal.fire({
                    title: 'Yakin hapus data?',
                    text: "Data tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {

                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }

                })

            }
        </script>

        <script>
            document.querySelectorAll('.rupiah').forEach(function(el) {

                el.addEventListener('keyup', function() {

                    let angka = this.value.replace(/[^,\d]/g, '').toString();
                    let split = angka.split(',');
                    let sisa = split[0].length % 3;
                    let rupiah = split[0].substr(0, sisa);
                    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    this.value = rupiah;

                });

            });
        </script>
    @endpush
@endsection
