<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {

        /* ===============================
        DATA STATISTIK
        =============================== */

        $total_obat = Obat::count();
        $total_supplier = Supplier::count();
        $total_user = User::count();
        $total_transaksi = Penjualan::count();

        $total_nilai_stok = DB::table('obats')
            ->selectRaw('SUM(stok * harga_beli) as total')
            ->value('total');


        /* ===============================
        PENJUALAN & PEMBELIAN HARI INI
        =============================== */

        $penjualan_hari_ini = Penjualan::whereDate('created_at', today())->sum('total');

        $pembelian_hari_ini = Pembelian::whereDate('created_at', today())->sum('total');

        $profit_hari_ini = $penjualan_hari_ini - $pembelian_hari_ini;


        /* ===============================
        DATA OBAT
        =============================== */

        $obat_kadaluarsa = Obat::whereDate('tanggal_exp', '<', now())->count();

        $obat_hampir_kadaluarsa = Obat::whereDate('tanggal_exp', '<=', now()->addDays(30))
            ->orderBy('tanggal_exp', 'asc')
            ->take(5)
            ->get();

        $stok_menipis = Obat::where('stok', '<', 10)
            ->take(5)
            ->get();


        /* ===============================
        TRANSAKSI TERBARU
        =============================== */

        $transaksi_terbaru = Penjualan::latest()
            ->take(5)
            ->get();


        /* ===============================
        TOP OBAT TERLARIS
        =============================== */

        $obat_terlaris = DB::table('detail_penjualan')
            ->join('obats', 'detail_penjualan.obat_id', '=', 'obats.id')
            ->select('obats.nama_obat', DB::raw('SUM(detail_penjualan.jumlah) as total'))
            ->groupBy('obats.nama_obat')
            ->orderByDesc('total')
            ->limit(5)
            ->get();


        /* ===============================
        GRAFIK PENJUALAN & PEMBELIAN
        =============================== */

        $bulan = [
            'Jan','Feb','Mar','Apr','Mei','Jun',
            'Jul','Agu','Sep','Okt','Nov','Des'
        ];

        $label = [];
        $data_penjualan = [];
        $data_pembelian = [];

        for ($i = 1; $i <= 12; $i++) {

            $label[] = $bulan[$i-1];

            $penjualan = Penjualan::whereMonth('created_at', $i)->sum('total');
            $pembelian = Pembelian::whereMonth('created_at', $i)->sum('total');

            $data_penjualan[] = $penjualan;
            $data_pembelian[] = $pembelian;
        }


        /* ===============================
        PRIORITAS FEFO
        =============================== */

        $prioritas_fefo = Obat::where('stok', '>', 0)
            ->orderBy('tanggal_exp', 'asc')
            ->take(5)
            ->get();


        return view('admin.dashboard', compact(

            'total_obat',
            'total_supplier',
            'total_user',
            'total_transaksi',
            'total_nilai_stok',

            'penjualan_hari_ini',
            'pembelian_hari_ini',
            'profit_hari_ini',

            'obat_kadaluarsa',
            'obat_hampir_kadaluarsa',

            'stok_menipis',

            'transaksi_terbaru',

            'obat_terlaris',

            'label',
            'data_penjualan',
            'data_pembelian',

            'prioritas_fefo'

        ));
    }
}