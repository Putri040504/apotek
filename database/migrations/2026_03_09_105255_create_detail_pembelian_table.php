<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detail_pembelian', function (Blueprint $table) {

            $table->id();

            $table->foreignId('pembelian_id');
            $table->foreignId('obat_id');

            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('subtotal');

            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_pembelian');
    }
};