<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{

protected $table = 'detail_penjualan';

protected $fillable = [
'penjualan_id',
'obat_id',
'jumlah',
'harga',
'subtotal'
];

public function penjualan()
{
    return $this->belongsTo(Penjualan::class);
}

public function obat()
{
    return $this->belongsTo(Obat::class);
}

}