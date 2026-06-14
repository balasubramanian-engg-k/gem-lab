<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaultStock extends Model
{
    protected $table = 'vault_stock';

    protected $fillable = ['amount', 'used_stock_offset'];

    protected $casts = [
        'amount' => 'decimal:3',
        'used_stock_offset' => 'decimal:3',
    ];

    /** Get the current vault stock amount (single row, id = 1). */
    public static function current(): float
    {
        $row = static::first();
        return $row ? (float) $row->amount : 0.0;
    }

    /** Get cumulative offset: add-stock amounts applied to reduce displayed used stock. */
    public static function getUsedStockOffset(): float
    {
        $row = static::first();
        return $row ? (float) $row->used_stock_offset : 0.0;
    }

    /**
     * When user adds stock: increase offset by that amount (capped at total invoice usage).
     * Effective used stock = total invoice usage - offset (so add reduces displayed "used").
     */
    public static function addToUsedStockOffset(float $amount): void
    {
        static::adjustUsedStockOffset($amount);
    }

    /**
     * Adjust offset by delta (positive or negative). Used when editing an add transaction.
     * newOffset = max(0, min(totalInvoiceUsage, currentOffset + delta)).
     */
    public static function adjustUsedStockOffset(float $delta): void
    {
        $row = static::first();
        if (! $row) {
            return;
        }
        $totalUsed = \App\Models\SilverStockTransaction::totalInvoiceUsage();
        $newOffset = max(0, min($totalUsed, (float) $row->used_stock_offset + $delta));
        $row->update(['used_stock_offset' => $newOffset]);
    }
}
