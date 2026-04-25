<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Obat;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Exports\LaporanObatExport;
use App\Exports\PembelianJenisExport;
use App\Exports\PembelianBulananExport;
use App\Exports\LaporanPenjualanBulanan;
use App\Exports\PenjualanJenisObatExport;

class LaporanController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | LAPORAN DATA OBAT
    |--------------------------------------------------------------------------
    */

   public function data_obat(Request $request)
{

    $bulan = $request->bulan;
    $tahun = $request->tahun;

    $query = Obat::with('kategori');

    if($bulan){
        $query->whereMonth('created_at',$bulan);
    }

    if($tahun){
        $query->whereYear('created_at',$tahun);
    }

    $obats = $query->get();

    return view(
        'admin.data_laporan.data_obat',
        compact('obats','bulan','tahun')
    );
}
    /*
    |--------------------------------------------------------------------------
    | EXPORT DATA OBAT
    |--------------------------------------------------------------------------
    */

    public function excel(Request $request)
{

    $bulan = $request->bulan;
    $tahun = $request->tahun;

    return Excel::download(
        new LaporanObatExport($bulan,$tahun),
        'laporan_data_obat.xlsx'
    );
}

   public function pdf(Request $request)
{

    $bulan = $request->bulan;
    $tahun = $request->tahun;

    $query = Obat::with('kategori');

    if($bulan){
        $query->whereMonth('created_at',$bulan);
    }

    if($tahun){
        $query->whereYear('created_at',$tahun);
    }

    $obats = $query->get();

    $pdf = Pdf::loadView(
        'admin.data_laporan.pdf_Ldata_obat',
        compact('obats','bulan','tahun')
    );

    return $pdf->download('laporan_data_obat.pdf');
}


    /*
    |--------------------------------------------------------------------------
    | PEMBELIAN PER BULAN
    |--------------------------------------------------------------------------
    */

    public function pembelian_bulanan(Request $request)
{

    $bulan = $request->bulan ?? date('m');
    $tahun = $request->tahun ?? date('Y');

    $data = DB::table('pembelian')
        ->join('suppliers','pembelian.supplier_id','=','suppliers.id')
        ->join('detail_pembelian','pembelian.id','=','detail_pembelian.pembelian_id')
        ->select(
            'pembelian.kode_transaksi',
            'pembelian.tanggal as tanggal_transaksi',
            'suppliers.nama_supplier as supplier',
            DB::raw('SUM(detail_pembelian.jumlah) as jumlah_item'),
            'pembelian.total as total_harga'
        )
        ->whereMonth('pembelian.tanggal',$bulan)
        ->whereYear('pembelian.tanggal',$tahun)
        ->groupBy(
            'pembelian.kode_transaksi',
            'pembelian.tanggal',
            'suppliers.nama_supplier',
            'pembelian.total'
        )
        ->get();

    $total = $data->sum('total_harga');

    return view(
        'admin.data_laporan.pembelian.pembelian_bulanan',
        compact('bulan','tahun','data','total')
    );
}


    /*
    |--------------------------------------------------------------------------
    | EXPORT PEMBELIAN BULANAN
    |--------------------------------------------------------------------------
    */

    public function pembelian_bulanan_pdf(Request $request)
{

    $bulan = $request->bulan;
    $tahun = $request->tahun;

    $data = DB::table('pembelian')
        ->join('suppliers','pembelian.supplier_id','=','suppliers.id')
        ->join('detail_pembelian','pembelian.id','=','detail_pembelian.pembelian_id')
        ->select(
            'pembelian.kode_transaksi',
            'pembelian.tanggal as tanggal_transaksi',
            'suppliers.nama_supplier as supplier',
            DB::raw('SUM(detail_pembelian.jumlah) as jumlah_item'),
            'pembelian.total as total_harga'
        )
        ->whereMonth('pembelian.tanggal',$bulan)
        ->whereYear('pembelian.tanggal',$tahun)
        ->groupBy(
            'pembelian.kode_transaksi',
            'pembelian.tanggal',
            'suppliers.nama_supplier',
            'pembelian.total'
        )
        ->get();

    $total = $data->sum('total_harga');

    $pdf = Pdf::loadView(
        'admin.data_laporan.pembelian.pdf_pembelian_bulanan',
        compact('data','total','bulan','tahun')
    );

    return $pdf->download('laporan_pembelian_'.$bulan.'_'.$tahun.'.pdf');
}


    public function pembelian_bulanan_excel(Request $request)
{

    $bulan = $request->bulan;
    $tahun = $request->tahun;

    $data = DB::table('pembelian')
        ->join('suppliers','pembelian.supplier_id','=','suppliers.id')
        ->join('detail_pembelian','pembelian.id','=','detail_pembelian.pembelian_id')
        ->select(
            'pembelian.kode_transaksi',
            'pembelian.tanggal as tanggal_transaksi',
            'suppliers.nama_supplier as supplier',
            DB::raw('SUM(detail_pembelian.jumlah) as jumlah_item'),
            'pembelian.total as total_harga'
        )
        ->whereMonth('pembelian.tanggal',$bulan)
        ->whereYear('pembelian.tanggal',$tahun)
        ->groupBy(
            'pembelian.kode_transaksi',
            'pembelian.tanggal',
            'suppliers.nama_supplier',
            'pembelian.total'
        )
        ->get();

    return Excel::download(
        new PembelianBulananExport($data,$bulan,$tahun),
        'laporan_pembelian_'.$bulan.'_'.$tahun.'.xlsx'
    );
}

    /*
    |--------------------------------------------------------------------------
    | PEMBELIAN BERDASARKAN JENIS OBAT
    |--------------------------------------------------------------------------
    */

    public function pembelian_jenis(Request $request)
{

    $bulan = $request->bulan ?? date('n');
    $tahun = $request->tahun ?? date('Y');
    $obat  = $request->obat;

    $obats = Obat::all();

    $data = [];
    $total = 0;

    if($obat){

        $data = DB::table('detail_pembelian')
            ->join('pembelian','detail_pembelian.pembelian_id','=','pembelian.id')
            ->join('obats','detail_pembelian.obat_id','=','obats.id')
            ->join('suppliers','pembelian.supplier_id','=','suppliers.id')
            ->select(
                'pembelian.kode_transaksi',
                'pembelian.tanggal as tanggal_transaksi',
                'suppliers.nama_supplier as supplier',
                'detail_pembelian.harga as harga_modal',
                'detail_pembelian.jumlah',
                'detail_pembelian.subtotal as total_pembelian'
            )
            ->whereMonth('pembelian.tanggal',$bulan)
            ->whereYear('pembelian.tanggal',$tahun)
            ->where('detail_pembelian.obat_id',$obat)
            ->get();

        $total = collect($data)->sum('total_pembelian');
    }

    return view(
        'admin.data_laporan.pembelian.pembelian_berdasarkan_jenis_obat',
        compact('data','total','bulan','tahun','obat','obats')
    );
}


    public function pembelian_jenis_excel(Request $request)
    {

        $bulan = $request->bulan;
        $obat  = $request->obat;

        return Excel::download(
            new PembelianJenisExport($bulan,$obat),
            'laporan_pembelian_jenis_obat.xlsx'
        );
    }


    public function pembelian_jenis_pdf(Request $request)
    {

        $bulan = $request->bulan;
        $obat  = $request->obat;

        $data = DB::table('detail_pembelian')
            ->join('pembelian','detail_pembelian.pembelian_id','=','pembelian.id')
            ->join('obats','detail_pembelian.obat_id','=','obats.id')
            ->join('suppliers','pembelian.supplier_id','=','suppliers.id')
            ->select(
                'pembelian.kode_transaksi',
                'pembelian.tanggal as tanggal_transaksi',
                'suppliers.nama_supplier as supplier',
                'detail_pembelian.harga',
                'detail_pembelian.jumlah',
                'detail_pembelian.subtotal'
            )
            ->whereMonth('pembelian.tanggal',$bulan)
            ->where('detail_pembelian.obat_id',$obat)
            ->get();

        $pdf = Pdf::loadView(
            'admin.data_laporan.pembelian.pdf_pembelian_jenis',
            compact('data','bulan','obat')
        );

        return $pdf->download('laporan_pembelian_jenis_obat.pdf');
    }


    /*
    |--------------------------------------------------------------------------
    | PENJUALAN PER BULAN
    |--------------------------------------------------------------------------
    */

    public function penjualan_perbulan(Request $request)
{

$bulan = $request->bulan ?? Carbon::now()->month;
$tahun = $request->tahun ?? Carbon::now()->year;

$data = DB::table('penjualan')
->join('detail_penjualan','penjualan.id','=','detail_penjualan.penjualan_id')
->select(
    'penjualan.no_transaksi',
    'penjualan.tanggal',
    DB::raw('SUM(detail_penjualan.jumlah) as jumlah_item'),
    'penjualan.total'
)
->whereMonth('penjualan.tanggal',$bulan)
->whereYear('penjualan.tanggal',$tahun)
->groupBy(
    'penjualan.id',
    'penjualan.no_transaksi',
    'penjualan.tanggal',
    'penjualan.total'
)
->get();

$total = $data->sum('total');

return view(
    'admin.data_laporan.penjualan.penjualan_per_bulan',
    compact('bulan','tahun','data','total')
);

}

/*
|--------------------------------------------------------------------------
| EXPORT PENJUALAN BULANAN EXCEL
|--------------------------------------------------------------------------
*/

public function penjualan_excel(Request $request)
{

$bulan = $request->bulan;
$tahun = $request->tahun;

return Excel::download(
new LaporanPenjualanBulanan($bulan,$tahun),
'laporan_penjualan_'.$bulan.'_'.$tahun.'.xlsx'
);

}

/*
|--------------------------------------------------------------------------
| EXPORT PENJUALAN BULANAN PDF
|--------------------------------------------------------------------------
*/

public function penjualan_pdf(Request $request)
{

$bulan = $request->bulan;
$tahun = $request->tahun;

$data = DB::table('penjualan')
->join('detail_penjualan','penjualan.id','=','detail_penjualan.penjualan_id')
->select(
    'penjualan.no_transaksi',
    'penjualan.tanggal',
    DB::raw('SUM(detail_penjualan.jumlah) as jumlah_item'),
    'penjualan.total'
)
->whereMonth('penjualan.tanggal',$bulan)
->whereYear('penjualan.tanggal',$tahun)
->groupBy(
    'penjualan.id',
    'penjualan.no_transaksi',
    'penjualan.tanggal',
    'penjualan.total'
)
->get();

$total = $data->sum('total');

$pdf = Pdf::loadView(
'admin.data_laporan.penjualan.pdf_penjualan_per_bulan',
compact('data','bulan','tahun','total')
);

return $pdf->download('laporan_penjualan_'.$bulan.'_'.$tahun.'.pdf');

}

    /*
    |--------------------------------------------------------------------------
    | PENJUALAN BERDASARKAN JENIS OBAT
    |--------------------------------------------------------------------------
    */

    public function penjualan_jenis(Request $request)
    {

        $bulan = $request->bulan;
        $obat  = $request->obat;

        $obats = Obat::all();

        $data = collect();
        $total = 0;

        if($bulan && $obat){

            $data = DB::table('detail_penjualan')
                ->join('penjualan','detail_penjualan.penjualan_id','=','penjualan.id')
                ->join('obats','detail_penjualan.obat_id','=','obats.id')
                ->select(
                    'penjualan.no_transaksi',
                    'penjualan.tanggal as tanggal_transaksi',
                    'detail_penjualan.harga',
                    'detail_penjualan.jumlah',
                    'detail_penjualan.subtotal as total_penjualan'
                )
                ->whereMonth('penjualan.tanggal',$bulan)
                ->where('detail_penjualan.obat_id',$obat)
                ->get();

            $total = $data->sum('total_penjualan');
        }

        return view(
            'admin.data_laporan.penjualan.penjualan_berdasarkan_jenis_obat',
            compact('bulan','obat','obats','data','total')
        );
    }

  public function penjualanJenisExcel(Request $request)
{

    $bulan = $request->bulan;
    $tahun = $request->tahun;
    $obat  = $request->obat;

    return Excel::download(
        new PenjualanJenisObatExport($bulan,$tahun,$obat),
        'laporan_penjualan_jenis_obat.xlsx'
    );

}
public function penjualanJenisPdf(Request $request)
{

    $bulan = $request->bulan;
    $tahun = $request->tahun;
    $obat  = $request->obat;

    $data = \DB::table('detail_penjualan')
    ->join('penjualan','detail_penjualan.penjualan_id','=','penjualan.id')
    ->join('obats','detail_penjualan.obat_id','=','obats.id')
    ->whereMonth('penjualan.tanggal',$bulan)
    ->whereYear('penjualan.tanggal',$tahun)
    ->where('obats.id',$obat)
    ->select(
        'penjualan.no_transaksi',
        'penjualan.tanggal',
        'detail_penjualan.harga',
        'detail_penjualan.jumlah',
        \DB::raw('(detail_penjualan.harga * detail_penjualan.jumlah) as total_penjualan')
    )
    ->get();

    $total = $data->sum('total_penjualan');

    $pdf = \PDF::loadView(
        'admin.data_laporan.penjualan.pdf_penjualan_jenis',
        compact('data','total')
    );

    return $pdf->download('laporan_penjualan_jenis.pdf');
}

    /*
    |--------------------------------------------------------------------------
    | PENJUALAN SELURUH OBAT
    |--------------------------------------------------------------------------
    */

    public function penjualan_semua(Request $request)
{

    $bulan = $request->bulan ?? date('n');
    $tahun = $request->tahun ?? date('Y');

    $data = DB::table('detail_penjualan')
        ->join('obats','obats.id','=','detail_penjualan.obat_id')
        ->join('penjualan','penjualan.id','=','detail_penjualan.penjualan_id')
        ->select(
            'obats.nama_obat',
            DB::raw('SUM(detail_penjualan.jumlah) as jumlah'),
            DB::raw('SUM(detail_penjualan.harga * detail_penjualan.jumlah) as total')
        )
        ->whereMonth('penjualan.tanggal',$bulan)
        ->whereYear('penjualan.tanggal',$tahun)
        ->groupBy('obats.nama_obat')
        ->get();

    $total = $data->sum('total');

    return view('admin.data_laporan.penjualan.penjualan_seluruh_obat',[
        'data'=>$data,
        'total'=>$total,
        'bulan'=>$bulan,
        'tahun'=>$tahun
    ]);
}

}