<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penjualan_batch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_penjualan_id')->constrained('detail_penjualan')->cascadeOnDelete();
            $table->foreignId('stok_batch_id')->constrained('stok_batches');
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan_batch');
    }
};
