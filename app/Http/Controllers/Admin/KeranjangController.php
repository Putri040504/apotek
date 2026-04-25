<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keranjang;
use App\Models\Obat;

class KeranjangController extends Controller
{

    public function store(Request $request)
    {

        $request->validate([
            'obat_id' => 'required',
            'qty' => 'required|numeric|min:1'
        ],[
            'obat_id.required' => 'Obat wajib dipilih',
            'qty.required' => 'Jumlah wajib diisi',
            'qty.numeric' => 'Jumlah harus angka',
            'qty.min' => 'Jumlah minimal 1'
        ]);

        // ambil data obat
        $obat = Obat::with('supplier')->findOrFail($request->obat_id);

        // cek apakah obat punya supplier
        if(!$obat->supplier_id){
            return redirect()->back()->with('error','Obat belum memiliki supplier');
        }

        // simpan ke keranjang
        Keranjang::create([
            'supplier_id' => $obat->supplier_id,
            'obat_id' => $obat->id,
            'qty' => $request->qty
        ]);

        return redirect()->back()->with('success','Obat berhasil ditambahkan ke keranjang');

    }


    public function destroy($id)
    {

        $keranjang = Keranjang::findOrFail($id);
        $keranjang->delete();

        return redirect()->back()->with('success','Item keranjang berhasil dihapus');

    }

}