<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Obat;
use App\Models\Kategori;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ObatExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ObatController extends Controller
{

    public function index()
    {
        $obat = Obat::with('kategori')->get();
        $kategori = Kategori::all();

        return view('admin.data_obat.index', compact('obat', 'kategori'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'kode_obat'   => 'required',
            'nama_obat'   => 'required',
            'kategori_id' => 'required',
            'tanggal_exp' => 'required',
            'stok'        => 'required',
            'harga_beli'  => 'required',
            'harga_jual'  => 'required'
        ]);

        Obat::create([
            'kode_obat'   => $request->kode_obat,
            'nama_obat'   => $request->nama_obat,
            'kategori_id' => $request->kategori_id,
            'tanggal_exp' => $request->tanggal_exp,
            'stok'        => $request->stok,
            'harga_beli'  => (int) str_replace('.', '', $request->harga_beli),
            'harga_jual'  => (int) str_replace('.', '', $request->harga_jual)
        ]);

        return redirect()->back()->with('success', 'Data obat berhasil ditambahkan');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_obat'   => 'required',
            'nama_obat'   => 'required',
            'kategori_id' => 'required',
            'tanggal_exp' => 'required',
            'stok'        => 'required',
            'harga_beli'  => 'required',
            'harga_jual'  => 'required'
        ]);

        $obat = Obat::findOrFail($id);

        $obat->update([
            'kode_obat'   => $request->kode_obat,
            'nama_obat'   => $request->nama_obat,
            'kategori_id' => $request->kategori_id,
            'tanggal_exp' => $request->tanggal_exp,
            'stok'        => $request->stok,
            'harga_beli'  => (int) str_replace('.', '', $request->harga_beli),
            'harga_jual'  => (int) str_replace('.', '', $request->harga_jual)
        ]);

        return redirect()->back()->with('success', 'Data obat berhasil diupdate');
    }


    public function destroy($id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return redirect()->back()->with('success', 'Data obat berhasil dihapus');
    }


    public function excel()
    {
        return Excel::download(new ObatExport, 'data_obat.xlsx');
    }


    public function pdf()
    {
        $obat = Obat::with('kategori')->get();

        $pdf = Pdf::loadView('admin.data_obat.pdf', compact('obat'));

        return $pdf->download('data_obat.pdf');
    }

}