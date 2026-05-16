@extends('kasir.layout.app')

@section('title', 'Dashboard')

@push('styles')
    <style>
        body {
            background: #f5f7f9;
        }

        /* CARD */
        .card {
            border-radius: 14px;
            border: 2px solid rgba(25, 135, 84, 0.45);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08) !important;
            transition: all .3s ease;
            cursor: pointer;
        }

        /* HOVER */
        .card:hover {
            transform: translateY(-6px);
            box-shadow:
                0 12px 25px rgba(0, 0, 0, 0.12),
                0 0 15px rgba(25, 135, 84, 0.35),
                0 0 30px rgba(25, 135, 84, 0.25),
                0 0 50px rgba(25, 135, 84, 0.15) !important;
        }

        /* HEADER CARD */
        .card-header {
            border-bottom: 2px solid rgba(25, 135, 84, 0.25);
            font-weight: 600;
            background: #198754 !important;
            color: white !important;
        }

        /* TABLE */
        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background: #198754;
            color: white;
        }

        .table thead th {
            font-weight: 600;
            text-align: center;
        }

        .table tbody tr:hover {
            background: #f1fdf6;
            transition: .2s;
        }

        /* PROGRESS */
        .progress {
            height: 10px;
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
        }

        /* CARD TEXT */
        .card-body h3 {
            font-size: 20px;
            font-weight: 600;
            margin-top: 4px;
        }

        .card-body small {
            font-size: 12px;
        }

        /* ICON CARD */
        .card-body i {
            font-size: 28px !important;
            opacity: 0.8;
        }

        /* TABLE */
        .table {
            font-size: 13px;
        }

        .table th {
            font-size: 12px;
            padding: 6px;
        }

        .table td {
            font-size: 12px;
            padding: 6px;
        }

        /* CARD HEADER */
        .card-header {
            font-size: 14px;
            padding: 10px 14px;
        }

        /* PROGRESS TEXT */
        .progress {
            height: 8px;
        }

        /* SCROLL AREA */
        .card-body {
            font-size: 13px;
        }
    </style>
@endpush


@section('content')

    <div class="mb-3">
        <a href="{{ route('kasir.pos') }}" class="btn btn-success btn-lg">
            <i class="bi bi-upc-scan"></i> Buka Kasir POS
        </a>
    </div>

    <div class="row g-4">

        <!-- STATISTIK -->

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Penjualan Hari Ini</small>
                        <h3>Rp {{ number_format($penjualan_hari_ini ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <i class="bi bi-cash-stack fs-1 text-success"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Total Transaksi Hari Ini</small>
                        <h3>{{ $total_transaksi ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-receipt fs-1 text-primary"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Obat Terjual</small>
                        <h3>{{ $obat_terjual ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-capsule fs-1 text-warning"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Stok Menipis</small>
                        <h3 class="text-danger">{{ $stok_menipis ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-1 text-danger"></i>
                </div>
            </div>
        </div>

    </div>


    <!-- GRAFIK + TOP OBAT -->

    <div class="row mt-4">

        <!-- GRAFIK -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">
                    Grafik Penjualan Harian
                </div>

                <div class="card-body">
                    <canvas id="chartHarian"></canvas>
                </div>
            </div>
        </div>


        <!-- TOP OBAT -->
        <div class="col-md-6">
            <div class="card shadow-sm">

                <div class="card-header fw-bold">
                    Top 5 Obat Terlaris Bulan Ini
                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <thead class="table-light text-center">
                            <tr>
                                <th>No</th>
                                <th>Nama Obat</th>
                                <th>Terjual</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($top_obat ?? [] as $o)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-start">{{ $o->obat->nama_obat ?? '-' }}</td>
                                    <td class="text-success fw-bold">{{ $o->total_terjual }}</td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        Belum ada data
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

    </div>



    <!-- PROGRESS STOK + EXPIRED -->

    <div class="row mt-4">

        <!-- PROGRESS STOK -->
        <div class="col-md-6">

            <div class="card shadow-sm">

                <div class="card-header fw-bold">
                    Progress Stok Obat
                </div>

                <div class="card-body" style="max-height:300px; overflow-y:auto;">

                    @foreach ($persentase_stok ?? [] as $o)
                        <div class="mb-3">

                            <div class="d-flex justify-content-between">
                                <small>{{ $o->nama_obat }}</small>
                                <small>{{ $o->stok }}</small>
                            </div>

                            <div class="progress">

                                <div class="progress-bar
                            @if ($o->stok <= 10) bg-danger
                            @elseif($o->stok <= 50)
                                bg-warning
                            @else
                                bg-success @endif
                        "
                                    style="
                        width:
                        @if ($o->stok <= 10) {{ ($o->stok / 10) * 100 }}%
                        @elseif($o->stok <= 50)
                            {{ ($o->stok / 50) * 100 }}%
                        @else
                            100% @endif
                        ">
                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>

        </div>



        <!-- MONITORING EXPIRED -->
        <div class="col-md-6">

            <div class="card shadow-sm">

                <div class="card-header bg-warning text-dark fw-bold">
                    Obat Akan Expired (≤ 6 Bulan)
                </div>

                <div class="card-body" style="max-height:300px; overflow-y:auto;">

                    <table class="table table-bordered text-center">

                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Stok</th>
                                <th>Expired</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($obat_akan_expired ?? [] as $o)
                                @php
                                    $exp = \Carbon\Carbon::parse($o->tanggal_exp);
                                @endphp

                                <tr>

                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-start">{{ $o->nama_obat }}</td>
                                    <td>{{ $o->stok }}</td>

                                    <td
                                        class="
@if ($exp->isPast()) text-danger fw-bold
@elseif($exp->diffInDays(now()) <= 30)
text-warning fw-bold @endif
">
                                        {{ $exp->format('d-m-Y') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-muted">
                                        Tidak ada obat
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>



    <!-- STOK MENIPIS + FEFO -->

    <div class="row mt-4">

        <!-- STOK MENIPIS -->
        <div class="col-md-6">

            <div class="card shadow-sm">

                <div class="card-header bg-danger text-white fw-bold">
                    Obat Stok Menipis
                </div>

                <div class="card-body" style="max-height:300px; overflow-y:auto;">

                    <table class="table table-bordered text-center">

                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Stok</th>
                                <th>Expired</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($obat_stok_menipis ?? [] as $o)
                                <tr>

                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-start">{{ $o->nama_obat }}</td>
                                    <td class="text-danger fw-bold">{{ $o->stok }}</td>
                                    @php
                                        $exp = \Carbon\Carbon::parse($o->tanggal_exp);
                                    @endphp

                                    <td class="@if ($exp->isPast()) text-danger fw-bold @endif">
                                        {{ $exp->format('d-m-Y') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-muted">
                                        Tidak ada stok menipis
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>



        <!-- FEFO -->
        <div class="col-md-6">

            <div class="card shadow-sm">

                <div class="card-header bg-secondary text-white fw-bold">
                    Prioritas Pengeluaran Stok (FEFO)
                </div>

                <div class="card-body" style="max-height:300px; overflow-y:auto;">

                    <table class="table table-bordered text-center">

                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Obat</th>
                                <th>Tanggal Masuk</th>
                                <th>Stok</th>
                                <th>Expired</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($fifo_obat ?? [] as $batch)
                                <tr>

                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-start">{{ $batch->obat->nama_obat ?? '-' }}</td>
                                    <td>{{ $batch->created_at?->format('d-m-Y') ?? '-' }}</td>
                                    <td>{{ $batch->jumlah }}</td>
                                    @php
                                        $exp = \Carbon\Carbon::parse($batch->tanggal_exp);
                                    @endphp

                                    <td
                                        class="
@if ($exp->isPast()) text-danger fw-bold
@elseif($exp->diffInDays(now()) <= 30)
text-warning fw-bold @endif
">
                                        {{ $exp->format('d-m-Y') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-muted">
                                        Tidak ada data
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let tanggal = {!! json_encode($tanggal ?? []) !!};
        let totalHarian = {!! json_encode($total_harian ?? []) !!};

        new Chart(document.getElementById('chartHarian'), {
            type: 'line',
            data: {
                labels: tanggal,
                datasets: [{
                    label: 'Penjualan Harian',
                    data: totalHarian
                }]
            }
        });
    </script>

@endsection
