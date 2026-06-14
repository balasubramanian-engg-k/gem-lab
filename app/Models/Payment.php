<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'renewal',
        'order_id',
        'payment_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'response_data',
        'paid_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'renewal' => 'boolean',
    ];
}
