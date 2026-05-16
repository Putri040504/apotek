@extends('admin.layout.app')

@section('title')
    Laporan Data Obat
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <form method="GET" class="d-flex gap-2 align-items-center">

                <select name="bulan" class="form-select form-select-sm" style="width:150px">

                    <option value="">Pilih Bulan</option>

                    <option value="1" {{ request('bulan') == 1 ? 'selected' : '' }}>Januari</option>
                    <option value="2" {{ request('bulan') == 2 ? 'selected' : '' }}>Februari</option>
                    <option value="3" {{ request('bulan') == 3 ? 'selected' : '' }}>Maret</option>
                    <option value="4" {{ request('bulan') == 4 ? 'selected' : '' }}>April</option>
                    <option value="5" {{ request('bulan') == 5 ? 'selected' : '' }}>Mei</option>
                    <option value="6" {{ request('bulan') == 6 ? 'selected' : '' }}>Juni</option>
                    <option value="7" {{ request('bulan') == 7 ? 'selected' : '' }}>Juli</option>
                    <option value="8" {{ request('bulan') == 8 ? 'selected' : '' }}>Agustus</option>
                    <option value="9" {{ request('bulan') == 9 ? 'selected' : '' }}>September</option>
                    <option value="10" {{ request('bulan') == 10 ? 'selected' : '' }}>Oktober</option>
                    <option value="11" {{ request('bulan') == 11 ? 'selected' : '' }}>November</option>
                    <option value="12" {{ request('bulan') == 12 ? 'selected' : '' }}>Desember</option>

                </select>

                <select name="tahun" class="form-select form-select-sm" style="width:120px">

                    @for ($i = 2026; $i <= 2035; $i++)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor

                </select>

                <button class="btn btn-success btn-sm">
                    <i class="bi bi-search"></i> Filter
                </button>

            </form>

        </div>


        <div>

            <a href="{{ route('laporan.obat.excel', [
                'bulan' => request('bulan'),
                'tahun' => request('tahun'),
            ]) }}"
                class="btn btn-outline-success me-2">

                <i class="bi bi-file-earmark-excel-fill"></i> Excel
            </a>


            <a href="{{ route('laporan.obat.pdf', [
                'bulan' => request('bulan'),
                'tahun' => request('tahun'),
            ]) }}"
                class="btn btn-outline-danger" target="_blank">

                <i class="bi bi-file-earmark-pdf-fill"></i> PDF
            </a>

        </div>

    </div>


    <div class="card shadow-sm border-0">
        <div class="card-body">

            <table id="tabelObat" class="table table-bordered table-hover table-sm text-center align-middle"
                style="font-size:14px;">

                <thead class="header-hijau">

                    <tr>
                        <th width="60">No</th>
                        <th>Kode Obat</th>
                        <th>Nama Obat</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga Jual</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach ($obats as $obat)
                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $obat->kode_obat }}</td>

                            <td class="text-start">{{ $obat->nama_obat }}</td>

                            <td>{{ $obat->kategori->nama_kategori ?? '-' }}</td>

                            <td>{{ $obat->stok }}</td>

                            <td class="text-end">
                                Rp {{ number_format($obat->harga_jual) }}
                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function() {

                $('#tabelObat').DataTable({

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
        </script>
    @endpush
@endsection
