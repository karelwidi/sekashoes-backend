<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Pembayaran ini milik satu Pesanan
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
