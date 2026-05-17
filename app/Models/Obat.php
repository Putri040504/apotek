<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Master obat (identitas & harga). Stok & kadaluarsa hanya di {@see StokBatch}.
 *
 * Kolom `stok` di obats = ringkasan total batch, di-sync lewat {@see syncFromBatches()}.
 */
class Obat extends Model
{
    public const MIN_DAYS_BEFORE_EXPIRY = 30;

    protected $table = 'obats';

    protected $fillable = [
        'kode_obat',
        'barcode',
        'nama_obat',
        'kategori_id',
        'stok',
        'harga_beli',
        'harga_jual',
    ];

    protected $casts = [
        'stok' => 'integer',
        'harga_beli' => 'integer',
        'harga_jual' => 'integer',
        'kategori_id' => 'integer',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function keranjang()
    {
        return $this->hasMany(Keranjang::class, 'obat_id');
    }

    public function keranjangPenjualan()
    {
        return $this->hasMany(KeranjangPenjualan::class, 'obat_id');
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'obat_id');
    }

    public function stokBatches()
    {
        return $this->hasMany(StokBatch::class, 'obat_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query scopes
    |--------------------------------------------------------------------------
    */

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stok', '>', 0);
    }

    public function scopeSellable(Builder $query): Builder
    {
        return $query->whereHas('stokBatches', fn (Builder $q) => $q->sellable());
    }

    public function scopeSearchTerm(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        $tokens = preg_split('/\s+/u', $term, -1, PREG_SPLIT_NO_EMPTY) ?: [$term];
        $primary = $tokens[0];
        $usePrefixOnly = count($tokens) === 1 && mb_strlen($primary) <= 2;

        foreach ($tokens as $token) {
            $escaped = static::escapeLike($token);
            $like = '%'.$escaped.'%';
            $prefix = $escaped.'%';

            $query->where(function (Builder $q) use ($like, $prefix, $usePrefixOnly) {
                if ($usePrefixOnly) {
                    $q->where('nama_obat', 'like', $prefix)
                        ->orWhere('kode_obat', 'like', $prefix)
                        ->orWhere('barcode', 'like', $prefix);
                } else {
                    $q->where('nama_obat', 'like', $like)
                        ->orWhere('kode_obat', 'like', $like)
                        ->orWhere('barcode', 'like', $like);
                }
            });
        }

        $escapedPrimary = static::escapeLike($primary);
        $prefixPrimary = $escapedPrimary.'%';
        $likePrimary = '%'.$escapedPrimary.'%';

        return $query
            ->orderByRaw(
                'CASE
                    WHEN nama_obat LIKE ? THEN 0
                    WHEN kode_obat LIKE ? OR barcode LIKE ? THEN 1
                    WHEN nama_obat LIKE ? THEN 2
                    ELSE 3
                END',
                [$prefixPrimary, $prefixPrimary, $prefixPrimary, $likePrimary]
            )
            ->orderBy('nama_obat');
    }

    protected static function escapeLike(string $value): string
    {
        return addcslashes($value, '%_\\');
    }

    /*
    |--------------------------------------------------------------------------
    | Kode obat internal (OB001, OB002, …)
    |--------------------------------------------------------------------------
    */

    /**
     * Kode berikutnya berdasarkan nomor OB tertinggi di database (bukan jumlah baris).
     * Contoh: sudah ada OB001–OB006 → mengembalikan OB007 meski hanya 5 baris tersisa.
     */
    public static function generateNextKodeObat(): string
    {
        $max = static::query()
            ->pluck('kode_obat')
            ->map(function (string $kode) {
                if (preg_match('/^OB(\d+)$/i', trim($kode), $m)) {
                    return (int) $m[1];
                }

                return 0;
            })
            ->max() ?? 0;

        $next = $max + 1;

        do {
            $kode = 'OB'.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
            $next++;
        } while (static::where('kode_obat', $kode)->exists());

        return $kode;
    }

    /*
    |--------------------------------------------------------------------------
    | Finder
    |--------------------------------------------------------------------------
    */

    /** Normalisasi string scan (EAN: hanya digit). */
    public static function normalizeScanCode(string $code): string
    {
        $code = trim($code);

        if ($code === '') {
            return '';
        }

        $digits = preg_replace('/\D/', '', $code);

        if ($digits !== '' && strlen($digits) >= 8) {
            return $digits;
        }

        return $code;
    }

    /** Nilai yang di-encode ke label fisik: barcode kemasan, atau kode internal. */
    public function scanCodeForLabel(): string
    {
        $barcode = trim((string) $this->barcode);

        return $barcode !== '' ? $barcode : $this->kode_obat;
    }

    public static function findByScanCode(string $code): ?self
    {
        $code = trim($code);

        if ($code === '') {
            return null;
        }

        $normalized = static::normalizeScanCode($code);

        $obat = static::query()
            ->where(function (Builder $q) use ($code, $normalized) {
                $q->where('kode_obat', $code)
                    ->orWhere('barcode', $code);

                if ($normalized !== $code) {
                    $q->orWhere('kode_obat', $normalized)
                        ->orWhere('barcode', $normalized);
                }
            })
            ->first();

        if (! $obat && is_numeric($code)) {
            $obat = static::find((int) $code);
        }

        return $obat;
    }

    /*
    |--------------------------------------------------------------------------
    | Batch & ringkasan stok
    |--------------------------------------------------------------------------
    */

    /** Batch FEFO pertama yang masih boleh dijual */
    public function fefoBatch(): ?StokBatch
    {
        return $this->stokBatches()->sellable()->orderFefo()->first();
    }

    /** Batch dengan stok > 0, expired terdekat (untuk tampilan) */
    public function earliestExpiryBatch(): ?StokBatch
    {
        return $this->stokBatches()->hasStock()->orderFefo()->first();
    }

    public function totalStokFromBatches(): int
    {
        return (int) $this->stokBatches()->sum('jumlah');
    }

    public function sellableStock(): int
    {
        return (int) $this->stokBatches()->sellable()->sum('jumlah');
    }

    /** Sinkronkan kolom stok di master dari total batch */
    public function syncFromBatches(): void
    {
        $this->forceFill([
            'stok' => $this->totalStokFromBatches(),
        ])->saveQuietly();
    }

    /**
     * Tambah stok ke batch (merge jika tanggal_exp sama pada obat yang sama).
     */
    public function addBatch(
        int $quantity,
        $tanggalExp,
        ?int $hargaBeli = null,
        ?int $supplierId = null
    ): StokBatch {
        $exp = Carbon::parse($tanggalExp)->toDateString();
        $hargaBeli = $hargaBeli ?? $this->harga_beli ?? 0;

        $batch = $this->stokBatches()->firstOrCreate(
            ['tanggal_exp' => $exp],
            [
                'jumlah' => 0,
                'harga_beli' => $hargaBeli,
                'supplier_id' => $supplierId,
            ]
        );

        $batch->increment('jumlah', $quantity);

        $updates = [];
        if ($hargaBeli > 0 && $batch->harga_beli === 0) {
            $updates['harga_beli'] = $hargaBeli;
        }
        if ($supplierId && ! $batch->supplier_id) {
            $updates['supplier_id'] = $supplierId;
        }
        if ($updates !== []) {
            $batch->update($updates);
        }

        $this->syncFromBatches();

        return $batch->fresh();
    }

    /**
     * Kurangi stok dengan algoritma FEFO (batch expired terdekat keluar dulu).
     *
     * @return array<int, array{stok_batch_id: int, jumlah: int}>
     */
    public function decreaseStockFefo(int $quantity): array
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Jumlah minimal 1');
        }

        $message = $this->saleValidationMessage($quantity);
        if ($message !== null) {
            throw new RuntimeException($message);
        }

        $remaining = $quantity;
        $allocations = [];

        $batches = $this->stokBatches()
            ->sellable()
            ->orderFefo()
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $take = min($remaining, $batch->jumlah);
            $batch->decrement('jumlah', $take);

            $allocations[] = [
                'stok_batch_id' => $batch->id,
                'jumlah' => $take,
            ];

            $remaining -= $take;
        }

        if ($remaining > 0) {
            throw new RuntimeException('Stok batch tidak mencukupi');
        }

        $this->syncFromBatches();

        return $allocations;
    }

    public function restoreStockFromAllocations(array $allocations): void
    {
        foreach ($allocations as $row) {
            StokBatch::where('id', $row['stok_batch_id'])->increment('jumlah', $row['jumlah']);
        }
        $this->syncFromBatches();
    }

    /*
    |--------------------------------------------------------------------------
    | Validasi penjualan (berdasarkan batch sellable)
    |--------------------------------------------------------------------------
    */

    public function daysUntilExpiry(): int
    {
        $batch = $this->stokBatches()->sellable()->orderFefo()->first();

        if ($batch) {
            return $batch->daysUntilExpiry();
        }

        $fallback = $this->stokBatches()->hasStock()->orderFefo()->first();

        return $fallback ? $fallback->daysUntilExpiry() : 0;
    }

    public function isExpired(): bool
    {
        return $this->sellableStock() === 0 && $this->stokBatches()->hasStock()->notExpired()->doesntExist();
    }

    public function isNearExpiry(): bool
    {
        if ($this->sellableStock() > 0) {
            return false;
        }

        return $this->stokBatches()->hasStock()->notExpired()->exists()
            && $this->stokBatches()->sellable()->doesntExist();
    }

    public function hasStock(): bool
    {
        return $this->sellableStock() > 0;
    }

    public function isSellable(): bool
    {
        return $this->sellableStock() > 0;
    }

    public function canSellQuantity(int $quantity): bool
    {
        return $quantity > 0 && $quantity <= $this->sellableStock();
    }

    public function saleValidationMessage(int $quantity): ?string
    {
        if ($quantity <= 0) {
            return 'Jumlah minimal 1';
        }

        $sellable = $this->sellableStock();

        if ($sellable === 0) {
            if ($this->stokBatches()->hasStock()->exists()) {
                $nearest = $this->stokBatches()->hasStock()->orderFefo()->first();

                return 'Obat akan expired dalam '.$nearest->daysUntilExpiry().' hari dan tidak boleh dijual';
            }

            if ($this->stokBatches()->whereDate('tanggal_exp', '<=', now())->exists()) {
                return 'Obat sudah expired dan tidak bisa dijual';
            }

            return 'Stok obat habis';
        }

        if ($quantity > $sellable) {
            return 'Stok obat tidak mencukupi (tersedia: '.$sellable.')';
        }

        return null;
    }

    public function assertSellable(int $quantity): void
    {
        $message = $this->saleValidationMessage($quantity);

        if ($message !== null) {
            throw new RuntimeException($message);
        }
    }

    public function subtotalForQuantity(int $quantity): int
    {
        return $this->harga_jual * $quantity;
    }

    /** @deprecated Use decreaseStockFefo */
    public function decreaseStock(int $quantity): array
    {
        return $this->decreaseStockFefo($quantity);
    }

    public function increaseStock(int $quantity, ?string $tanggalExp = null, ?int $supplierId = null): void
    {
        $exp = $tanggalExp
            ?? $this->earliestExpiryBatch()?->tanggal_exp?->toDateString()
            ?? now()->addYear()->toDateString();

        $this->addBatch($quantity, $exp, $this->harga_beli, $supplierId);
    }

    public function toLookupArray(): array
    {
        return [
            'id' => $this->id,
            'kode_obat' => $this->kode_obat,
            'barcode' => $this->barcode,
            'scan_code' => $this->scanCodeForLabel(),
            'nama_obat' => $this->nama_obat,
            'harga_beli' => $this->harga_beli,
            'harga_jual' => $this->harga_jual,
            'stok' => $this->stok,
            'sellable_stock' => $this->sellableStock(),
        ];
    }

    public function toPosArray(): array
    {
        $fefoBatch = $this->stokBatches()->sellable()->orderFefo()->first();

        return [
            'id' => $this->id,
            'kode_obat' => $this->kode_obat,
            'barcode' => $this->barcode,
            'nama_obat' => $this->nama_obat,
            'stok' => $this->sellableStock(),
            'stok_total' => $this->totalStokFromBatches(),
            'harga_jual' => $this->harga_jual,
            'harga_jual_formatted' => 'Rp '.number_format($this->harga_jual, 0, ',', '.'),
            'tanggal_exp' => $fefoBatch?->tanggal_exp?->format('Y-m-d'),
            'tanggal_exp_fefo' => $fefoBatch?->tanggal_exp?->format('Y-m-d'),
            'batch_count' => $this->stokBatches()->hasStock()->count(),
            'days_until_expiry' => $this->daysUntilExpiry(),
            'is_sellable' => $this->isSellable(),
        ];
    }
}
