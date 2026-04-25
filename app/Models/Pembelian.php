<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';

    protected $fillable = [
        'kode_transaksi',
        'tanggal',
        'supplier_id',
        'total',
        'obat_id',
        'qty'
    ];
    
    /*
    |--------------------------------------------------------------------------
    | Relasi Supplier
    |--------------------------------------------------------------------------
    */

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi Detail Pembelian
    |--------------------------------------------------------------------------
    */

    public function detail()
    {
        return $this->hasMany(DetailPembelian::class, 'pembelian_id');
    }
    
    public function obat()
{
    return $this->belongsTo(Obat::class);
}
}