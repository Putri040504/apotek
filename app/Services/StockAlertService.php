<?php

namespace App\Services;

use App\Models\Obat;
use App\Models\StokBatch;
use Illuminate\Support\Collection;

/**
 * Notifikasi stok & kadaluarsa.
 * Stok habis/menipis: kolom obats.stok (cache dari batch).
 * Kadaluarsa: stok_batches (sumber kebenaran).
 */
class StockAlertService
{
    public static function outOfStock(): Collection
    {
        return Obat::where('stok', 0)->orderBy('nama_obat')->get();
    }

    public static function lowStock(int $min = 1, int $max = 5): Collection
    {
        return Obat::whereBetween('stok', [$min, $max])->orderBy('stok')->get();
    }

    public static function nearExpiryBatches(int $days = 30): Collection
    {
        return StokBatch::with('obat')
            ->nearExpiry($days)
            ->orderFefo()
            ->get();
    }

    public static function expiredBatches(): Collection
    {
        return StokBatch::with('obat')
            ->expired()
            ->orderFefo()
            ->get();
    }
}
