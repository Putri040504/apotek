<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualanBatch extends Model
{
    protected $table = 'detail_penjualan_batch';

    protected $fillable = [
        'detail_penjualan_id',
        'stok_batch_id',
        'jumlah',
    ];

    public function detailPenjualan()
    {
        return $this->belongsTo(DetailPenjualan::class, 'detail_penjualan_id');
    }

    public function stokBatch()
    {
        return $this->belongsTo(StokBatch::class, 'stok_batch_id');
    }
}
