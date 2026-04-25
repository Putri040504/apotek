<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{

    protected $table = 'keranjangs';

    protected $fillable = [
        'supplier_id',
        'obat_id',
        'qty'
    ];


    // RELASI KE OBAT
    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }

    // RELASI KE SUPPLIER
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

}