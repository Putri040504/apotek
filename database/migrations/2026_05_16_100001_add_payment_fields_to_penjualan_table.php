<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->string('metode_bayar')->default('tunai')->after('bayar');
            $table->string('status')->default('paid')->after('metode_bayar');
            $table->string('midtrans_order_id')->nullable()->after('status');
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id');
            $table->integer('kembalian')->default(0)->after('midtrans_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'metode_bayar',
                'status',
                'midtrans_order_id',
                'midtrans_transaction_id',
                'kembalian',
            ]);
        });
    }
};
