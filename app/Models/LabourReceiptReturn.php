<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabourReceiptReturn extends Model
{
    protected $fillable = [
        'labour_receipt_id',
        'count_received',
        'weight_received',
        'wastage_grams',
        'amount_paid',
        'received_at',
        'remarks',
    ];

    protected $casts = [
        'weight_received' => 'decimal:3',
        'wastage_grams' => 'decimal:3',
        'amount_paid' => 'decimal:2',
        'received_at' => 'date',
    ];

    public function labourReceipt(): BelongsTo
    {
        return $this->belongsTo(LabourReceipt::class);
    }
}
