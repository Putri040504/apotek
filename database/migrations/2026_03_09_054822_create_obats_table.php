<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('obats', function (Blueprint $table) {
        $table->id();
        $table->string('kode_obat');
        $table->string('nama_obat');
        $table->date('tanggal_exp');
        $table->foreignId('kategori_id')->constrained('kategoris');
        $table->integer('stok');
        $table->integer('harga_beli');
        $table->integer('harga_jual');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obats');
    }
};
