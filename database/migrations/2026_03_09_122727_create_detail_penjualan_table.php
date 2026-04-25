<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('detail_penjualan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('penjualan_id');
        $table->foreignId('obat_id');
        $table->integer('jumlah');
        $table->integer('harga');
        $table->integer('subtotal');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
    }
};
