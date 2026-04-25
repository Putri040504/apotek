<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Keranjang;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\DetailPembelian;
use Illuminate\Http\Request;

class PembelianController extends Controller
{

    public function index()
    {

        $pembelian = Pembelian::with('supplier','detail.obat')
            ->orderBy('id','asc')
            ->get();

        $supplier = Supplier::all();
        $obat = Obat::all();

        // ambil semua keranjang
        $keranjang = Keranjang::with(['obat','supplier'])->get();

        return view('admin.data_pembelian.index', compact(
            'pembelian',
            'supplier',
            'obat',
            'keranjang'
        ));
    }


    public function store(Request $request)
    {

        $request->validate([
            'supplier_id' => 'required',
            'obat_id' => 'required',
            'qty' => 'required|numeric|min:1'
        ]);

        Keranjang::create([
            'supplier_id' => $request->supplier_id,
            'obat_id' => $request->obat_id,
            'qty' => $request->qty
        ]);

        return redirect()->route('pembelian.index')
            ->with('success','Obat berhasil ditambahkan ke keranjang');
    }


    // HAPUS DATA PEMBELIAN
    public function destroy($id)
    {

        $pembelian = Pembelian::findOrFail($id);

        // ambil semua detail pembelian
        $detail = DetailPembelian::where('pembelian_id',$pembelian->id)->get();

        // kembalikan stok obat
        foreach($detail as $d){

            $obat = Obat::find($d->obat_id);

            if($obat){
                $obat->stok -= $d->jumlah;
                $obat->save();
            }

        }

        // hapus detail pembelian
        DetailPembelian::where('pembelian_id',$pembelian->id)->delete();

        // hapus pembelian
        $pembelian->delete();

        return redirect()->route('pembelian.index')
            ->with('success','Data pembelian berhasil dihapus');

    }


    public function checkout(Request $request)
    {

        $ids = $request->input('keranjang_id', []);

        if(empty($ids)){
            return redirect()->back()->with('error','Centang minimal 1 item keranjang');
        }

        $keranjang = Keranjang::with('obat')
            ->whereIn('id',$ids)
            ->get();

        if($keranjang->isEmpty()){
            return redirect()->back()->with('error','Keranjang tidak ditemukan');
        }

        $supplier_id = $keranjang->first()->supplier_id;

        if(!$supplier_id){
            return redirect()->back()->with('error','Supplier tidak ditemukan');
        }

        $total = 0;

        foreach($keranjang as $k){
            $total += $k->obat->harga_beli * $k->qty;
        }

        // GENERATE KODE TRANSAKSI
        $last = Pembelian::orderBy('id','desc')->first();

        if(!$last){
            $kode = "PB0001";
        }else{
            $number = intval(substr($last->kode_transaksi,2)) + 1;
            $kode = "PB".str_pad($number,4,'0',STR_PAD_LEFT);
        }

        // SIMPAN PEMBELIAN
        $pembelian = Pembelian::create([
            'kode_transaksi' => $kode,
            'tanggal' => now(),
            'supplier_id' => $supplier_id,
            'total' => $total
        ]);

        foreach($keranjang as $k){

            $obat = $k->obat;

            $harga = $obat->harga_beli;
            $subtotal = $harga * $k->qty;

            DetailPembelian::create([
                'pembelian_id' => $pembelian->id,
                'obat_id' => $obat->id,
                'harga' => $harga,
                'jumlah' => $k->qty,
                'subtotal' => $subtotal
            ]);

            // tambah stok obat
            $obat->stok += $k->qty;
            $obat->save();

            // hapus dari keranjang
            Keranjang::destroy($k->id);
        }

        return redirect()->route('pembelian.index')
            ->with('success','Checkout berhasil');
    }


    public function cetak($id)
    {
        $pembelian = Pembelian::with('detail.obat','supplier')->findOrFail($id);

        return view('admin.data_pembelian.cetak', compact('pembelian'));
    }

}