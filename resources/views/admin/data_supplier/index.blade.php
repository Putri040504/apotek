@extends('admin.layout.app')

@section('title')
    Data Supplier
@endsection

@section('content')
    <style>
        #tabelSupplier {
            font-size: 13px;
        }

        #tabelSupplier th {
            font-size: 12px;
            padding: 6px;
        }

        #tabelSupplier td {
            padding: 5px;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Data
            </button>

        </div>

        <div>

            <a href="{{ route('supplier.excel') }}" class="btn btn-outline-success me-2">
                <i class="bi bi-file-earmark-excel-fill"></i> Excel
            </a>

            <a href="{{ route('supplier.pdf') }}" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF
            </a>

        </div>

    </div>


    <div class="card">
        <div class="card-body">

            <table id="tabelSupplier" class="table table-bordered align-middle w-100">

                <thead class="header-hijau text-start align-top">
                    <tr>
                        <th width="60">No</th>
                        <th>Kode Supplier</th>
                        <th>Nama Supplier</th>
                        <th>Nama Obat</th>
                        <th>Alamat</th>
                        <th>Email</th>
                        <th>No Telp</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($supplier as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $s->kode_supplier }}</td>
                            <td>{{ $s->nama_supplier }}</td>

                            <td>
                                @php
                                    $namaObat = $s->stokBatches->pluck('obat.nama_obat')->filter()->unique();
                                @endphp
                                @forelse($namaObat as $nama)
                                    {{ $nama }}<br>
                                @empty
                                    {{ $s->nama_obat ?? '-' }}
                                @endforelse
                            </td>

                            <td>{{ $s->alamat }}</td>
                            <td>{{ $s->email }}</td>
                            <td>{{ $s->no_telp }}</td>

                            <td>
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                    data-bs-target="#modalEdit{{ $s->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form id="delete-form-{{ $s->id }}" action="{{ route('supplier.destroy', $s->id) }}"
                                    method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $s->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>

                        </tr>

                        @include('admin.data_supplier.modal_edit')
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>

    @include('admin.data_supplier.modal_tambah')

    @push('scripts')
        <script>
            $(document).ready(function() {

                $('#tabelSupplier').DataTable({

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
    @endpush
@endsection
