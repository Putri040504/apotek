<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ObatExport;
use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Obat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ObatController extends Controller
{
    public function index()
    {
        $obat = Obat::with([
            'kategori',
            'stokBatches' => fn ($q) => $q->hasStock()->orderFefo(),
        ])->get();
        $kategori = Kategori::all();
        $nextKodeObat = Obat::generateNextKodeObat();

        return view('admin.data_obat.index', compact('obat', 'kategori', 'nextKodeObat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'nullable|string|max:50|unique:obats,barcode',
            'nama_obat' => 'required',
            'kategori_id' => 'required',
            'tanggal_exp' => 'required|date',
            'stok' => 'required|integer|min:0',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
        ], [
            'barcode.unique' => 'Barcode kemasan sudah dipakai obat lain.',
        ]);

        $hargaBeli = (int) str_replace('.', '', $request->harga_beli);
        $hargaJual = (int) str_replace('.', '', $request->harga_jual);
        $barcode = $this->normalizeBarcodeInput($request->barcode);
        $kodeObat = Obat::generateNextKodeObat();

        $obat = Obat::create([
            'kode_obat' => $kodeObat,
            'barcode' => $barcode,
            'nama_obat' => $request->nama_obat,
            'kategori_id' => $request->kategori_id,
            'stok' => 0,
            'harga_beli' => $hargaBeli,
            'harga_jual' => $hargaJual,
        ]);

        if ((int) $request->stok > 0) {
            $obat->addBatch((int) $request->stok, $request->tanggal_exp, $hargaBeli, null);
        }

        return redirect()->back()->with('success', 'Data obat '.$kodeObat.' berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_obat' => 'required',
            'kategori_id' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
            'barcode' => 'nullable|string|max:50|unique:obats,barcode,'.$id,
        ], [
            'barcode.unique' => 'Barcode kemasan sudah dipakai obat lain.',
        ]);

        $obat = Obat::findOrFail($id);
        $barcode = $this->normalizeBarcodeInput($request->input('barcode'));

        $obat->fill([
            'barcode' => $barcode,
            'nama_obat' => $request->nama_obat,
            'kategori_id' => $request->kategori_id,
            'harga_beli' => (int) str_replace('.', '', $request->harga_beli),
            'harga_jual' => (int) str_replace('.', '', $request->harga_jual),
        ]);
        $obat->save();

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

    private function normalizeBarcodeInput(?string $barcode): ?string
    {
        $barcode = trim((string) $barcode);

        if ($barcode === '') {
            return null;
        }

        return Obat::normalizeScanCode($barcode);
    }
}
