<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KategoriExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PembelianBulananExport;

class KategoriController extends Controller
{

    public function index()
    {
         $kategori = Kategori::orderBy('id','asc')->get();

        return view('admin.data_kategori.index', compact('kategori'));
    }


    public function store(Request $request)
    {

        // VALIDASI
        $request->validate([
            'nama_kategori' => 'required|unique:kategoris,nama_kategori'
        ],[
            'nama_kategori.required' => 'Nama kategori wajib diisi',
            'nama_kategori.unique' => 'Nama kategori sudah ada!'
        ]);


        // PREFIX KODE
        $prefix = 'KT';


        // AMBIL DATA TERAKHIR
        $last = Kategori::orderBy('id','asc')->first();


        if($last){

            $number = (int) substr($last->kode_kategori,2);
            $kode = $prefix.str_pad($number+1,3,'0',STR_PAD_LEFT);

        }else{

            $kode = $prefix.'001';

        }


        // SIMPAN DATA
        Kategori::create([
            'kode_kategori' => $kode,
            'nama_kategori' => trim($request->nama_kategori)
        ]);


        return redirect('/admin/kategori')
        ->with('success','Data kategori berhasil ditambahkan');

    }


    public function update(Request $request, $id)
    {

        $kategori = Kategori::findOrFail($id);


        // VALIDASI
        $request->validate([
            'nama_kategori' => 'required|unique:kategoris,nama_kategori,'.$id
        ],[
            'nama_kategori.required' => 'Nama kategori wajib diisi',
            'nama_kategori.unique' => 'Nama kategori sudah ada!'
        ]);


        $kategori->update([
            'nama_kategori' => trim($request->nama_kategori)
        ]);


        return redirect('/admin/kategori')
        ->with('success','Data kategori berhasil diupdate');

    }


    public function destroy($id)
    {

        $kategori = Kategori::findOrFail($id);

        $kategori->delete();


        return redirect('/admin/kategori')
        ->with('success','Data kategori berhasil dihapus');

    }


    public function exportExcel()
    {

        return Excel::download(new KategoriExport, 'data_kategori_apotek.xlsx');

    }


    public function exportPdf()
    {

        $kategori = Kategori::orderBy('kode_kategori')->get();

        $pdf = Pdf::loadView('admin.data_kategori.pdf', compact('kategori'));

        return $pdf->download('laporan_kategori_obat.pdf');

    }

}