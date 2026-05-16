<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Sumber kebenaran stok & tanggal kadaluarsa per masuk barang (FEFO).
 */
class StokBatch extends Model
{
    public const MIN_DAYS_BEFORE_EXPIRY = 30;

    protected $fillable = [
        'obat_id',
        'supplier_id',
        'jumlah',
        'tanggal_exp',
        'harga_beli',
    ];

    protected $casts = [
        'tanggal_exp' => 'date',
        'jumlah' => 'integer',
        'harga_beli' => 'integer',
        'supplier_id' => 'integer',
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function scopeHasStock(Builder $query): Builder
    {
        return $query->where('jumlah', '>', 0);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->whereDate('tanggal_exp', '>', now());
    }

    public function scopeNotNearExpiry(Builder $query): Builder
    {
        return $query->whereDate('tanggal_exp', '>', now()->addDays(self::MIN_DAYS_BEFORE_EXPIRY));
    }

    /** Batch masih ada stok dan tanggal_exp sudah lewat */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->hasStock()->whereDate('tanggal_exp', '<', now()->startOfDay());
    }

    /** Batch masih ada stok, kadaluarsa dalam N hari (termasuk hari ini) */
    public function scopeNearExpiry(Builder $query, int $days = 30): Builder
    {
        $today = now()->startOfDay();

        return $query->hasStock()
            ->whereDate('tanggal_exp', '>=', $today)
            ->whereDate('tanggal_exp', '<=', $today->copy()->addDays($days));
    }

    /** Batch yang boleh dijual di POS (FEFO source) */
    public function scopeSellable(Builder $query): Builder
    {
        return $query->hasStock()->notNearExpiry();
    }

    /** Urutan FEFO: expired terdekat dulu */
    public function scopeOrderFefo(Builder $query): Builder
    {
        return $query->orderBy('tanggal_exp', 'asc')->orderBy('id', 'asc');
    }

    public function daysUntilExpiry(): int
    {
        return (int) now()->startOfDay()->diffInDays(
            Carbon::parse($this->tanggal_exp)->startOfDay(),
            false
        );
    }

    public function isSellable(): bool
    {
        return $this->jumlah > 0 && $this->daysUntilExpiry() > self::MIN_DAYS_BEFORE_EXPIRY;
    }

    /** Daftar prioritas FEFO untuk dashboard */
    public static function fefoPriorities(int $limit = 5)
    {
        return static::with('obat.kategori')
            ->sellable()
            ->orderFefo()
            ->limit($limit)
            ->get();
    }

    /** Batch hampir kadaluarsa (untuk panel dashboard), urut FEFO */
    public static function nearExpiryBatches(int $limit = 5, int $days = 30)
    {
        return static::with('obat')
            ->nearExpiry($days)
            ->orderFefo()
            ->limit($limit)
            ->get();
    }

    public static function expiredBatchCount(): int
    {
        return static::expired()->count();
    }
}
