<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{

    protected $table = 'detail_pembelian';

    protected $fillable = [
        'pembelian_id',
        'obat_id',
        'jumlah',
        'harga',
        'subtotal'
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

}