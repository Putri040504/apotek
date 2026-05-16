<?php

namespace App\Providers;

use App\Models\Obat;
use Carbon\Carbon;
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

        // Jangan query DB saat artisan (migrate, package:discover, queue, dll.)
        // — penting agar Docker build tidak butuh database.
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
            if (! Schema::hasTable('obats')) {
                View::share([
                    'stok_habis' => $empty,
                    'stok_menipis' => $empty,
                    'obat_hampir_expired' => $empty,
                    'obat_expired' => $empty,
                ]);

                return;
            }

            $today = Carbon::today();

            $stok_habis = Obat::where('stok', 0)->get();
            $stok_menipis = Obat::whereBetween('stok', [1, 5])->get();
            $obat_hampir_expired = Obat::whereBetween('tanggal_exp', [
                $today,
                $today->copy()->addDays(30),
            ])->get();
            $obat_expired = Obat::whereDate('tanggal_exp', '<', $today)->get();

            View::share([
                'stok_habis' => $stok_habis,
                'stok_menipis' => $stok_menipis,
                'obat_hampir_expired' => $obat_hampir_expired,
                'obat_expired' => $obat_expired,
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
