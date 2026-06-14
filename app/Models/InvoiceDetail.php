<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    /** Silver stock: invoice usage = sum(gross_weight) - sum(ston_weight) * this factor. */
    public const SILVER_STOCK_STONE_MULTIPLIER = 0.2;

    protected $fillable = [
        'product_sl_no',
        'product_name',
        'invoice_id',
        'stone',
        'ring_size',
        'ston_weight',
        'gross_weight',
        'size',
        'silvercost',
        'stonecost',
        'making_charge',
        'rate',
    ];

    /**
     * Get the invoice that owns this detail.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the stone associated with this detail.
     */
    public function stoneData()
    {
        return $this->belongsTo(Stone::class, 'stone');
    }
}
