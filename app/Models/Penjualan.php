<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'total',
        'bayar',
        'user_id',
        'metode_bayar',
        'status',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'kembalian',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function detail()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
