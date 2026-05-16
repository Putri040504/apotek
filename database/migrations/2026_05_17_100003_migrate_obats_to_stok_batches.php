<?php

use App\Models\Obat;
use App\Models\StokBatch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Obat::query()->orderBy('id')->each(function (Obat $obat) {
            if ($obat->stok <= 0) {
                return;
            }

            StokBatch::firstOrCreate(
                [
                    'obat_id' => $obat->id,
                    'tanggal_exp' => $obat->tanggal_exp,
                ],
                [
                    'jumlah' => $obat->stok,
                    'harga_beli' => $obat->harga_beli ?? 0,
                ]
            );

            $obat->syncFromBatches();
        });
    }

    public function down(): void
    {
        DB::table('stok_batches')->truncate();
    }
};
