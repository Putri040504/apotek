<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{

    protected $table = 'suppliers';

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'alamat',
        'email',
        'no_telp'
    ];

    public function stokBatches()
    {
        return $this->hasMany(StokBatch::class, 'supplier_id');
    }

}