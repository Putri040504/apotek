<?php

namespace App\Providers;

use App\Services\StockAlertService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $empty = collect();

        if ($this->app->runningInConsole()) {
            View::share([
                'stok_habis' => $empty,
                'stok_menipis' => $empty,
                'obat_hampir_expired' => $empty,
                'obat_expired' => $empty,
            ]);

            return;
        }

        try {
            if (! Schema::hasTable('obats') || ! Schema::hasTable('stok_batches')) {
                View::share([
                    'stok_habis' => $empty,
                    'stok_menipis' => $empty,
                    'obat_hampir_expired' => $empty,
                    'obat_expired' => $empty,
                ]);

                return;
            }

            View::share([
                'stok_habis' => StockAlertService::outOfStock(),
                'stok_menipis' => StockAlertService::lowStock(),
                'obat_hampir_expired' => StockAlertService::nearExpiryBatches(),
                'obat_expired' => StockAlertService::expiredBatches(),
            ]);
        } catch (\Throwable) {
            View::share([
                'stok_habis' => $empty,
                'stok_menipis' => $empty,
                'obat_hampir_expired' => $empty,
                'obat_expired' => $empty,
            ]);
        }
    }
}
