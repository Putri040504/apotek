<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ADMIN CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PembelianController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\KeranjangController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\PenjualanController;
use App\Http\Controllers\Kasir\ProfileController as KasirProfileController;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    if (auth()->check()) {

        if (auth()->user()->role == 'admin') {
            return redirect('/admin/dashboard');
        }

        if (auth()->user()->role == 'kasir') {
            return redirect('/kasir/dashboard');
        }

    }

    return redirect('/login');

});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTE
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class,'index'])
        ->name('admin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class,'index'])
        ->name('admin.profile');

    Route::post('/profile/update', [ProfileController::class,'update'])
        ->name('admin.profile.update');


    /*
    |--------------------------------------------------------------------------
    | MASTER DATA
    |--------------------------------------------------------------------------
    */

    // KATEGORI
    Route::resource('kategori', KategoriController::class);

    Route::get('kategori/excel', [KategoriController::class,'exportExcel'])
        ->name('kategori.excel');

    Route::get('kategori/pdf', [KategoriController::class,'exportPdf'])
        ->name('kategori.pdf');


    // OBAT
    Route::resource('obat', ObatController::class)->except(['show']);

    Route::get('obat/excel', [ObatController::class,'excel'])
        ->name('obat.excel');

    Route::get('obat/pdf', [ObatController::class,'pdf'])
        ->name('obat.pdf');


    // SUPPLIER
    Route::resource('supplier', SupplierController::class);

    Route::get('supplier/excel', [SupplierController::class,'excel'])
        ->name('supplier.excel');

    Route::get('supplier/pdf', [SupplierController::class,'pdf'])
        ->name('supplier.pdf');


    /*
    |--------------------------------------------------------------------------
    | PEMBELIAN
    |--------------------------------------------------------------------------
    */

    Route::resource('pembelian', PembelianController::class);

    Route::post('pembelian/checkout', [PembelianController::class,'checkout'])
        ->name('pembelian.checkout');
    
    Route::get('/admin/pembelian/cetak/{id}', [PembelianController::class,'cetak'])
        ->name('pembelian.cetak');


    /*
    |--------------------------------------------------------------------------
    | KERANJANG PEMBELIAN
    |--------------------------------------------------------------------------
    */

    Route::post('keranjang/store', [KeranjangController::class,'store'])
        ->name('keranjang.store');

    Route::delete('keranjang/{id}', [KeranjangController::class,'destroy'])
        ->name('keranjang.destroy');

    
    /*
    |--------------------------------------------------------------------------
    | PENGGUNA
    |--------------------------------------------------------------------------
    */

    Route::resource('pengguna', UserController::class)->except(['show']);

    Route::get('pengguna/excel', [UserController::class,'excel'])
        ->name('pengguna.excel');

    Route::get('pengguna/pdf', [UserController::class,'pdf'])
        ->name('pengguna.pdf');


    /*
    |--------------------------------------------------------------------------
    | LAPORAN
    |--------------------------------------------------------------------------
    */

    Route::prefix('laporan')->group(function () {

        // LAPORAN DATA OBAT
        Route::get('/obat', [LaporanController::class,'data_obat'])
            ->name('laporan.obat');

        Route::get('/obat/excel', [LaporanController::class,'excel'])
            ->name('laporan.obat.excel');

        Route::get('/obat/pdf', [LaporanController::class,'pdf'])
            ->name('laporan.obat.pdf');


        // LAPORAN PEMBELIAN
        Route::get('/pembelian-bulanan', [LaporanController::class,'pembelian_bulanan'])
            ->name('laporan.pembelian_bulanan');

        Route::get('/pembelian-jenis', [LaporanController::class,'pembelian_jenis'])
            ->name('laporan.pembelian_jenis');

        Route::get('/pembelian-bulanan/pdf', [LaporanController::class,'pembelian_bulanan_pdf'])
            ->name('laporan.pembelian.pdf');
        
        Route::get('/laporan/pembelian/excel',[LaporanController::class,'pembelian_bulanan_excel'])
            ->name('laporan.pembelian.excel');   
            
        Route::get('/pembelian-jenis/excel', [LaporanController::class,'pembelianJenisExcel'])
    ->name('laporan.pembelian.jenis.excel');

Route::get('/pembelian-jenis/pdf', [LaporanController::class,'pembelianJenisPdf'])
    ->name('laporan.pembelian.jenis.pdf');    

        // LAPORAN PENJUALAN
Route::get('/penjualan-bulanan', [LaporanController::class,'penjualan_perbulan'])
    ->name('laporan.penjualan_bulanan');

Route::get('/penjualan-jenis', [LaporanController::class,'penjualan_jenis'])
    ->name('laporan.penjualan_jenis');

Route::get('/penjualan', [LaporanController::class,'penjualan_semua'])
    ->name('laporan.penjualan');

Route::get('/penjualan/excel', [LaporanController::class,'penjualan_excel'])
    ->name('laporan.penjualan.excel');

Route::get('/penjualan/pdf', [LaporanController::class,'penjualan_pdf'])
    ->name('laporan.penjualan.pdf');
    });

    // EXPORT PENJUALAN BERDASARKAN JENIS OBAT

Route::get('/penjualan-jenis/excel', [LaporanController::class,'penjualanJenisExcel'])
    ->name('laporan.penjualan.jenis.excel');

Route::get('/penjualan-jenis/pdf', [LaporanController::class,'penjualanJenisPdf'])
    ->name('laporan.penjualan.jenis.pdf');
});


/*
|--------------------------------------------------------------------------
| KASIR ROUTE
|--------------------------------------------------------------------------
*/

Route::prefix('kasir')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', [KasirDashboardController::class,'index'])
        ->name('kasir.dashboard');

    Route::get('/penjualan', [PenjualanController::class,'index'])
        ->name('kasir.penjualan');

    Route::post('/keranjang/store', [PenjualanController::class,'store'])
        ->name('kasir.keranjang.store');

    Route::delete('/keranjang/{id}', [PenjualanController::class,'destroy'])
        ->name('kasir.keranjang.destroy');

    Route::post('/penjualan/checkout', [PenjualanController::class,'checkout'])
        ->name('penjualan.checkout');

    Route::get('/penjualan/cetak/{id}', [PenjualanController::class,'cetak'])
        ->name('penjualan.cetak');

    Route::get('/riwayat', [PenjualanController::class,'riwayat'])
        ->name('riwayat.penjualan');
    
    Route::get('/riwayat/detail/{id}', [PenjualanController::class,'detailModal'])
    ->name('riwayat.detail');

    Route::get('/admin/pembelian/cetak/{id}', [PembelianController::class,'cetak'])
    ->name('pembelian.cetak');

    Route::get('/riwayat/excel',[PenjualanController::class,'exportExcel']);
    Route::get('/riwayat/pdf',[PenjualanController::class,'exportPDF']);

   Route::get('/profile', [KasirProfileController::class,'index'])
    ->name('kasir.profile');

Route::post('/profile/update', [KasirProfileController::class,'update'])
    ->name('kasir.profile.update');

     

});
 
 
require __DIR__.'/auth.php';