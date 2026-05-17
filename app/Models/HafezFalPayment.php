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
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'integer'
    ];
}
