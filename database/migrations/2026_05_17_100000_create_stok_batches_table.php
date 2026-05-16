<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obats')->cascadeOnDelete();
            $table->integer('jumlah')->default(0);
            $table->date('tanggal_exp');
            $table->integer('harga_beli')->default(0);
            $table->timestamps();

            $table->unique(['obat_id', 'tanggal_exp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_batches');
    }
};
