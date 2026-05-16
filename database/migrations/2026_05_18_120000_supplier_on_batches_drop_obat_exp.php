<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_batches', function (Blueprint $table) {
            if (! Schema::hasColumn('stok_batches', 'supplier_id')) {
                $table->foreignId('supplier_id')
                    ->nullable()
                    ->after('obat_id')
                    ->constrained('suppliers')
                    ->nullOnDelete();
            }
        });

        if (Schema::hasColumn('obats', 'tanggal_exp')) {
            Schema::table('obats', function (Blueprint $table) {
                $table->dropColumn('tanggal_exp');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('obats', 'tanggal_exp')) {
            Schema::table('obats', function (Blueprint $table) {
                $table->date('tanggal_exp')->nullable();
            });
        }

        Schema::table('stok_batches', function (Blueprint $table) {
            if (Schema::hasColumn('stok_batches', 'supplier_id')) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            }
        });
    }
};
