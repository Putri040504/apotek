@extends('admin.layout.app')

@section('title')
    Laporan Penjualan Obat
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <form method="GET" class="d-flex">

                <select name="bulan" class="form-select me-2" style="width:200px">

                    <option value="">Pilih Bulan</option>

                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->locale('id')->translatedFormat('F') }}
                        </option>
                    @endfor

                </select>


                <select name="tahun" class="form-select me-2" style="width:200px">

                    <option value="">Pilih Tahun</option>

                    @for ($t = \Carbon\Carbon::now()->year; $t <= \Carbon\Carbon::now()->year + 5; $t++)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endfor

                </select>

                <button class="btn btn-success">
                    <i class="bi bi-search"></i> Check Data
                </button>

            </form>
        </div>


        @if ($bulan)
            <div class="d-flex gap-2">

                <a href="{{ url('/admin/laporan/penjualan/excel?bulan=' . $bulan . '&tahun=' . $tahun) }}"
                    class="btn btn-outline-success">

                    <i class="bi bi-file-earmark-excel"></i> Excel

                </a>

                <a href="{{ url('/admin/laporan/penjualan/pdf?bulan=' . $bulan . '&tahun=' . $tahun) }}"
                    class="btn btn-outline-danger">

                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF

                </a>

            </div>
        @endif

    </div>


    @if ($bulan)
        <div class="card shadow-sm border-0">

            <div class="card-body">

                <table id="tabelPenjualan" class="table table-bordered table-hover table-sm text-center align-middle"
                    style="font-size:14px;">

                    <thead class="header-hijau">

                        <tr>
                            <th width="60">No</th>
                            <th>No Transaksi</th>
                            <th>Tanggal</th>
                            <th>Jumlah Item</th>
                            <th>Total</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($data as $d)
                            <tr>

                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $d->no_transaksi }}</td>
                                <td>{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('d F Y') }}</td>
                                <td>{{ $d->jumlah_item }}</td>
                                <td class="text-end">Rp {{ number_format($d->total, 0, ',', '.') }}</td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

                <div class="alert alert-success mt-3">

                    Total penjualan bulan ini
                    <b>Rp {{ number_format($total, 0, ',', '.') }}</b>

                </div>

            </div>
        </div>
    @endif


    @push('scripts')
        <script>
            $(document).ready(function() {

                $('#tabelPenjualan').DataTable({

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
