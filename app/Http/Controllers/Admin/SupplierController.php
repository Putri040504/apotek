<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Supplier;
use App\Models\Obat;
use App\Exports\SupplierExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierController extends Controller
{

    public function index()
{
    $supplier = Supplier::with('obat')->get();
    $obat = Obat::all();

    return view('admin.data_supplier.index', compact('supplier','obat'));
}


    public function store(Request $request)
    {

        $request->validate([
            'kode_supplier' => 'required',
            'nama_supplier' => 'required',
            'obat_id' => 'required',
            'alamat' => 'required',
            'email' => 'required',
            'no_telp' => 'required'
        ]);

        Supplier::create([
            'kode_supplier' => $request->kode_supplier,
            'nama_supplier' => $request->nama_supplier,
            'obat_id' => $request->obat_id,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'no_telp' => $request->no_telp
        ]);

        return redirect()->back()->with('success','Data supplier berhasil ditambahkan');

    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'kode_supplier' => 'required',
            'nama_supplier' => 'required',
            'obat_id' => 'required',
            'alamat' => 'required',
            'email' => 'required',
            'no_telp' => 'required'
        ]);

        $supplier = Supplier::findOrFail($id);

        $supplier->update([
            'kode_supplier' => $request->kode_supplier,
            'nama_supplier' => $request->nama_supplier,
            'obat_id' => $request->obat_id,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'no_telp' => $request->no_telp
        ]);

        return redirect()->back()->with('success','Data supplier berhasil diupdate');

    }


    public function destroy($id)
    {

        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->back()->with('success','Data supplier berhasil dihapus');

    }
    
    public function excel()
{
    return Excel::download(new SupplierExport, 'data_supplier.xlsx');
}

public function pdf()
{
    $supplier = Supplier::with('obat')->get();

    $pdf = Pdf::loadView('admin.data_supplier.pdf', compact('supplier'));

    return $pdf->download('data_supplier.pdf');
}
}