<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\KeranjangPenjualan;
use App\Models\DetailPenjualan;
use App\Models\Obat;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\RiwayatPenjualanExport;

class PenjualanController extends Controller
{

    public function index()
    {

        $penjualan = Penjualan::with('detail.obat')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->orderBy('tanggal','asc')
            ->get();

        $keranjang = KeranjangPenjualan::with('obat')->get();

        $obat = Obat::all();

        return view('kasir.data_penjualan.index', compact(
            'penjualan',
            'keranjang',
            'obat'
        ));
    }


    public function store(Request $request)
{

    $obat = Obat::findOrFail($request->obat_id);

    $today = now();
    $exp = Carbon::parse($obat->tanggal_exp);

    $selisih = $today->diffInDays($exp, false);

    // SUDAH EXPIRED
    if($selisih <= 0){
        return redirect()->back()->with('error','Obat sudah expired dan tidak bisa dijual');
    }

    // HAMPIR EXPIRED (MISAL 30 HARI)
    if($selisih <= 30){
        return redirect()->back()->with('error','Obat akan expired dalam '.$selisih.' hari dan tidak boleh dijual');
    }

    KeranjangPenjualan::create([
        'obat_id' => $request->obat_id,
        'jumlah' => $request->jumlah
    ]);

    return redirect()->back()->with('success','Obat berhasil masuk keranjang');
}


    public function checkout(Request $request)
    {

        if (!$request->has('keranjang_id')) {
            return redirect()->back()->with('error', 'Pilih keranjang terlebih dahulu');
        }

        $keranjang = KeranjangPenjualan::with('obat')
            ->whereIn('id', $request->keranjang_id)
            ->get();

        $total = 0;

        foreach ($keranjang as $k) {
            $total += $k->jumlah * $k->obat->harga_jual;
        }

        // bersihkan format rupiah
        $bayar = str_replace('.', '', $request->bayar);

        // validasi uang kurang
        if($bayar < $total){
            return redirect()->back()->with('error','Uang pembeli tidak cukup');
        }

        $penjualan = Penjualan::create([
            'no_transaksi' => 'TRX' . date('YmdHis'),
            'tanggal' => now(),
            'total' => $total,
            'bayar' => $bayar
        ]);

        foreach ($keranjang as $k) {

            $sisa = $k->jumlah;

            // FEFO berdasarkan tanggal exp
            $obatFIFO = Obat::where('kode_obat', $k->obat->kode_obat)
               ->whereDate('tanggal_exp','>',now())
               ->orderBy('tanggal_exp', 'asc')
               ->get();

            foreach ($obatFIFO as $obat) {

                if ($sisa <= 0) break;

                if ($obat->stok <= 0) continue;

                $ambil = min($obat->stok, $sisa);

                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'obat_id' => $obat->id,
                    'jumlah' => $ambil,
                    'harga' => $obat->harga_jual,
                    'subtotal' => $ambil * $obat->harga_jual
                ]);

                $obat->stok -= $ambil;
                $obat->save();

                $sisa -= $ambil;
            }

            if ($sisa > 0) {
                return redirect()->back()->with('error','Stok obat tidak mencukupi');
            }

            $k->delete();
        }

        return redirect('/kasir/penjualan')
            ->with('success', 'Checkout berhasil');
    }


    public function destroy($id)
    {

        KeranjangPenjualan::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Keranjang dihapus');
    }


    public function cetak($id)
    {

        $penjualan = Penjualan::with('detail.obat')->findOrFail($id);

        return view('kasir.data_penjualan.cetak', compact('penjualan'));
    }

    public function riwayat(Request $request)
    {

        $bulan = $request->bulan ?? now()->month;
$tahun = $request->tahun ?? now()->year;

        $query = Penjualan::with('detail.obat');

        if($bulan){
            $query->whereMonth('tanggal', $bulan);
        }

        if($tahun){
            $query->whereYear('tanggal', $tahun);
        }

        $riwayat = $query->orderBy('tanggal','asc')->get();

        return view('kasir.data_riwayat.index', compact(
            'riwayat',
            'bulan',
            'tahun'
        ));

    }

    public function detailModal($id)
    {
        $penjualan = \App\Models\Penjualan::with('detail.obat')->findOrFail($id);

        return view('kasir.data_riwayat.detail_modal', compact('penjualan'));
    }

    public function cetakRiwayat($id)
    {

        $penjualan = Penjualan::with('detail.obat')->findOrFail($id);

        return view('kasir.data_riwayat.cetak', compact('penjualan'));

    }

    public function exportExcel(Request $request)
    {

        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        return Excel::download(
            new RiwayatPenjualanExport($bulan,$tahun),
            'riwayat_penjualan.xlsx'
        );

    }

    public function exportPDF(Request $request)
    {

        $bulan = $request->bulan ?? now()->month;
$tahun = $request->tahun ?? now()->year;

        $query = Penjualan::with('detail.obat');

        if($bulan){
            $query->whereMonth('tanggal',$bulan);
        }

        if($tahun){
            $query->whereYear('tanggal',$tahun);
        }

        $riwayat = $query->get();

        $pdf = Pdf::loadView('kasir.data_riwayat.pdf', compact('riwayat'));

        return $pdf->download('riwayat_penjualan.pdf');

    }

}