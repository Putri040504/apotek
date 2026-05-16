<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Pembelian;
use App\Models\Obat;
use App\Models\StokBatch;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{

public function index()
{

// ============================
// PENJUALAN HARI INI
// ============================

$penjualan_hari_ini = Penjualan::whereDate('tanggal', today())->sum('total');


// ============================
// TOTAL TRANSAKSI HARI INI
// ============================

$total_transaksi = Penjualan::whereDate('tanggal', today())->count();


// ============================
// TOTAL OBAT TERJUAL
// ============================

$obat_terjual = DetailPenjualan::whereDate('created_at', today())
->sum('jumlah');


// ============================
// STOK MENIPIS
// ============================

$stok_menipis = Obat::where('stok','>',0)
->where('stok','<=',10)
->count();

$obat_stok_menipis = Obat::where('stok','>',0)
->where('stok','<=',10)
->orderBy('stok','asc')
->limit(5)
->get();


// ============================
// MONITORING EXPIRED
// ============================

// OBAT AKAN EXPIRED (6 bulan)

$obat_akan_expired = Obat::whereDate('tanggal_exp','<=',Carbon::now()->addMonths(6))
->whereDate('tanggal_exp','>=',Carbon::now())
->orderBy('tanggal_exp','asc')
->limit(5)
->get();


// OBAT SUDAH EXPIRED

$obat_expired = Obat::whereDate('tanggal_exp','<',Carbon::now())
->limit(5)
->get();


// ============================
// FEFO (FIRST EXPIRED FIRST OUT) — per batch stok
// ============================

$fifo_obat = StokBatch::fefoPriorities(5);

// ============================
// TRANSAKSI TERBARU
// ============================

$transaksi_terbaru = Penjualan::latest()
->limit(5)
->get();


// ============================
// GRAFIK PENJUALAN HARIAN (7 hari)
// ============================

$grafik_harian = Penjualan::select(
DB::raw('DATE(tanggal) as tanggal'),
DB::raw('SUM(total) as total')
)
->whereDate('tanggal','>=',Carbon::now()->subDays(7))
->groupBy('tanggal')
->orderBy('tanggal','asc')
->get();

$tanggal = [];
$total_harian = [];

foreach($grafik_harian as $g){

$tanggal[] = Carbon::parse($g->tanggal)->format('d M');
$total_harian[] = $g->total;

}


// ============================
// OBAT TERLARIS BULAN INI
// ============================

$obat_terlaris_bulan = DetailPenjualan::select(
'obat_id',
DB::raw('SUM(jumlah) as total_terjual')
)
->whereMonth('created_at',Carbon::now()->month)
->groupBy('obat_id')
->orderByDesc('total_terjual')
->with('obat')
->first();


// ============================
// TOP 5 OBAT TERLARIS BULAN INI
// ============================

$top_obat = DetailPenjualan::select(
'obat_id',
DB::raw('SUM(jumlah) as total_terjual')
)
->whereMonth('created_at', Carbon::now()->month)
->whereYear('created_at', Carbon::now()->year)
->whereHas('obat') // tambahkan ini
->groupBy('obat_id')
->orderByDesc('total_terjual')
->with('obat')
->limit(5)
->get();


// ============================
// PERSENTASE STOK OBAT
// ============================

$obat = Obat::select('nama_obat','stok')
->limit(5)
->get();

$max = $obat->max('stok');

$persentase_stok = $obat->map(function($o) use ($max){

$o->persen = $max > 0 ? round(($o->stok / $max) * 100) : 0;

return $o;

});


// ============================
// RETURN VIEW
// ============================

return view('kasir.dashboard',compact(
'penjualan_hari_ini',
'total_transaksi',
'obat_terjual',
'stok_menipis',
'obat_stok_menipis',
'obat_akan_expired',
'obat_expired',
'fifo_obat',
'transaksi_terbaru',
'tanggal',
'total_harian',
'obat_terlaris_bulan',
'top_obat',
'persentase_stok'
));

}

}