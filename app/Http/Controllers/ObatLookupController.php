<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;

class ObatLookupController extends Controller
{
    public function __invoke(Request $request)
    {
        $kode = trim($request->get('kode', ''));

        if ($kode === '') {
            return response()->json(['error' => 'Kode barcode kosong'], 400);
        }

        $obat = Obat::findByScanCode($kode);

        if (! $obat) {
            return response()->json(['error' => 'Obat tidak ditemukan'], 404);
        }

        if ($request->get('context') === 'pos') {
            $error = $obat->saleValidationMessage(1);
            if ($error) {
                return response()->json(['error' => $error], 422);
            }

            return response()->json($obat->toPosArray());
        }

        return response()->json($obat->toLookupArray());
    }
}
