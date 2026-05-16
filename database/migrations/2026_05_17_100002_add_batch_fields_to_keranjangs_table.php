<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keranjangs', function (Blueprint $table) {
            if (! Schema::hasColumn('keranjangs', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('keranjangs', 'tanggal_exp')) {
                $table->date('tanggal_exp')->nullable()->after('qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('keranjangs', function (Blueprint $table) {
            if (Schema::hasColumn('keranjangs', 'tanggal_exp')) {
                $table->dropColumn('tanggal_exp');
            }
        });
    }
};
