<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{

    protected $table = 'obats';

    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'kategori_id',
        'supplier_id',
        'tanggal_exp',
        'stok',
        'harga_beli',
        'harga_jual'
    ];

    // RELASI KE KATEGORI
    public function kategori()
    {
        return $this->belongsTo(Kategori::class,'kategori_id');
    }

    // RELASI KE SUPPLIER
    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    // RELASI KE KERANJANG
    public function keranjang()
    {
        return $this->hasMany(Keranjang::class,'obat_id');
    }

}