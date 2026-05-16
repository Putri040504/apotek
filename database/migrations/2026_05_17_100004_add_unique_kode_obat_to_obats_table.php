<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $dupes = DB::table('obats')
            ->select('kode_obat')
            ->groupBy('kode_obat')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('kode_obat');

        foreach ($dupes as $kode) {
            $rows = DB::table('obats')->where('kode_obat', $kode)->orderBy('id')->get();
            foreach ($rows->skip(1) as $index => $row) {
                DB::table('obats')
                    ->where('id', $row->id)
                    ->update(['kode_obat' => $kode.'-DUP'.($index + 1)]);
            }
        }

        Schema::table('obats', function (Blueprint $table) {
            $table->unique('kode_obat');
        });
    }

    public function down(): void
    {
        Schema::table('obats', function (Blueprint $table) {
            $table->dropUnique(['kode_obat']);
        });
    }
};
