<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HafezFalPayment extends Model
{
    protected $fillable = [
        'chat_id',
        'payload',
        'amount',
        'status', // pending, paid, failed
        'bale_payment_id',
        'payment_info',
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'integer',
        'payment_info' => 'json' // اگر ستون از نوع JSON هست
    ];

}
