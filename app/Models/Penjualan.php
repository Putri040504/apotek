<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetailPenjualan;

class Penjualan extends Model
{

protected $table = 'penjualan';

protected $fillable = [
'no_transaksi',
'tanggal',
'total',
'bayar'
];

public function detail()
{
    return $this->hasMany(DetailPenjualan::class,'penjualan_id');
}

}