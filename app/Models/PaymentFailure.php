<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentFailure extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 
        'payment_id', 
        'error_code', 
        'error_description', 
        'error_source', 
        'error_step', 
        'error_reason', 
        'payload'
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
