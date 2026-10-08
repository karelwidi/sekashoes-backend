<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Pesanan ini milik satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Pesanan ini menggunakan satu Service (Layanan)
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Satu pesanan memiliki satu detail pembayaran (One-to-One)
    public function payment() 
    {
        return $this->hasOne(Payment::class);
    }
}
