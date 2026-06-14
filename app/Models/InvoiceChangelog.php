<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceChangelog extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
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

    /**
     * Get the invoice that owns this changelog.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the user who made this change.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
