@extends('kasir.layout.app')

@section('title', 'Riwayat Penjualan')

@section('content')

    <style>
        #tabelRiwayat {
            font-size: 13px;
        }

        #tabelRiwayat thead th {
            background: #198754;
            color: #fff;
            text-align: center;
            font-size: 12px;
            padding: 8px;
            vertical-align: middle;
        }

        #tabelRiwayat tbody tr:hover {
            background: #e9f7ef;
        }

        .dataTables_filter input {
            border: 1px solid #198754 !important;
        }

        .page-item.active .page-link {
            background: #198754 !important;
            border-color: #198754 !important;
        }

        .page-link {
            color: #198754 !important;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #198754 !important;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;
        }

        .btn-detail {
            background: #fff !important;
            border: 1px solid #198754 !important;
            color: #198754 !important;
        }

        .btn-detail:hover {
            background: #198754 !important;
            color: #fff !important;
        }
    </style>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="mb-0 fw-bold text-success">Riwayat Penjualan</h5>
            <small class="text-muted">
                {{ \Carbon\Carbon::create()->month($bulan)->locale('id')->translatedFormat('F') }} {{ $tahun }}
            </small>
        </div>
        <a href="{{ route('kasir.pos') }}" class="btn btn-success">
            <i class="bi bi-upc-scan"></i> Buka Kasir POS
        </a>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form method="GET" class="d-flex flex-wrap gap-2">
            <select name="bulan" class="form-select" style="width:160px">
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ (int) $bulan === $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->locale('id')->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            <select name="tahun" class="form-select" style="width:120px">
                @for ($t = now()->year; $t >= now()->year - 5; $t--)
                    <option value="{{ $t }}" {{ (int) $tahun === $t ? 'selected' : '' }}>{{ $t }}
                    </option>
                @endfor
            </select>
            <button type="submit" class="btn btn-success">Filter</button>
        </form>
        <div>
            <a href="{{ url('/kasir/riwayat/excel?bulan=' . $bulan . '&tahun=' . $tahun) }}" class="btn btn-outline-success me-1">
                <i class="bi bi-file-earmark-excel-fill"></i> Excel
            </a>
            <a href="{{ url('/kasir/riwayat/pdf?bulan=' . $bulan . '&tahun=' . $tahun) }}" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-success">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelRiwayat" class="table table-bordered table-striped text-center align-middle">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kode Transaksi</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Nama Obat</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayat as $r)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $r->no_transaksi }}</td>
                                <td data-order="{{ \Carbon\Carbon::parse($r->tanggal)->timestamp }}">
                                    {{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    <span class="badge {{ $r->metode_bayar === 'qris' ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ strtoupper($r->metode_bayar ?? 'tunai') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($r->status === 'paid')
                                        <span class="badge bg-success">Lunas</span>
                                    @elseif($r->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Batal</span>
                                    @endif
                                </td>
                                <td class="text-start">
                                    @forelse($r->detail as $d)
                                        • {{ $d->obat->nama_obat ?? '-' }}<br>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td>{{ $r->detail->sum('jumlah') }}</td>
                                <td class="text-end fw-semibold" data-order="{{ $r->total }}">
                                    Rp {{ number_format($r->total, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-detail btn-detail-modal"
                                            data-id="{{ $r->id }}" data-bs-toggle="modal"
                                            data-bs-target="#modalDetail" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if ($r->status === 'paid')
                                            <a href="{{ route('penjualan.cetak', $r->id) }}"
                                                class="btn btn-sm btn-outline-success" target="_blank" title="Cetak struk">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-receipt"></i> Detail Transaksi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailPenjualan">Loading...</div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const table = $('#tabelRiwayat');
            if (table.length && !$.fn.DataTable.isDataTable(table)) {
                table.DataTable({
                    pageLength: 10,
                    order: [
                        [2, 'desc']
                    ],
                    autoWidth: false,
                    columnDefs: [{
                        orderable: false,
                        targets: [8]
                    }],
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        emptyTable: 'Belum ada penjualan pada periode ini',
                        zeroRecords: 'Data tidak ditemukan',
                        info: 'Menampilkan _START_–_END_ dari _TOTAL_',
                        paginate: {
                            previous: '‹',
                            next: '›'
                        }
                    }
                });
            }
        });

        document.querySelectorAll('.btn-detail-modal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('detailPenjualan').innerHTML = 'Loading...';
                fetch('/kasir/riwayat/detail/' + id)
                    .then(r => r.text())
                    .then(html => {
                        document.getElementById('detailPenjualan').innerHTML = html;
                    });
            });
        });
    </script>
@endpush
