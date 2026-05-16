<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Obat;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'obat_id' => 'required|exists:obats,id',
            'qty' => 'required|numeric|min:1',
            'exp' => 'required|date',
        ], [
            'obat_id.required' => 'Obat wajib dipilih',
            'qty.required' => 'Jumlah wajib diisi',
            'qty.numeric' => 'Jumlah harus angka',
            'qty.min' => 'Jumlah minimal 1',
        ]);

        Obat::findOrFail($request->obat_id);

        Keranjang::create([
            'supplier_id' => $request->supplier_id,
            'obat_id' => $request->obat_id,
            'qty' => $request->qty,
            'tanggal_exp' => $request->exp,
        ]);

        return redirect()->back()->with('success', 'Obat berhasil ditambahkan ke keranjang');
    }

    public function destroy($id)
    {
        Keranjang::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Item keranjang berhasil dihapus');
    }
}
