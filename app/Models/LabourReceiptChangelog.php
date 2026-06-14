<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabourReceiptChangelog extends Model
{
    protected $fillable = [
        'labour_receipt_id',
        'user_id',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'field_label',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function labourReceipt(): BelongsTo
    {
        return $this->belongsTo(LabourReceipt::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
