<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeranjangPenjualan extends Model
{
    protected $table = 'keranjang_penjualan';

    protected $fillable = [
        'obat_id',
        'jumlah'
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}