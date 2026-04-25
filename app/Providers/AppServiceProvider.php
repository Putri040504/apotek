<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Obat;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {

        $today = Carbon::today();

        // stok habis
        $stok_habis = Obat::where('stok',0)->get();

        // stok menipis (1 - 5)
        $stok_menipis = Obat::whereBetween('stok',[1,5])->get();

        // hampir expired (0 - 30 hari)
        $obat_hampir_expired = Obat::whereBetween('tanggal_exp',[
            $today,
            $today->copy()->addDays(30)
        ])->get();

        // sudah expired
        $obat_expired = Obat::whereDate('tanggal_exp','<',$today)->get();

        View::share([
            'stok_habis' => $stok_habis,
            'stok_menipis' => $stok_menipis,
            'obat_hampir_expired' => $obat_hampir_expired,
            'obat_expired' => $obat_expired
        ]);

    }
}