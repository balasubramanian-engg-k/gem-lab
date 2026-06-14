<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SilverStockTransaction extends Model
{
    public const TYPE_ADD = 'add';
    public const TYPE_SELL = 'sell';
    public const TYPE_INVOICE_USAGE = 'invoice_usage';
    public const TYPE_VAULT_UPDATE = 'vault_update';

    protected $fillable = [
        'type',
        'amount',
        'transaction_date',
        'remarks',
        'invoice_id',
        'user_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:3',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Total silver added (all "add" transactions) */
    public static function totalAdded(): float
    {
        return (float) static::where('type', self::TYPE_ADD)->sum('amount');
    }

    /** Total silver sold (all "sell" transactions) */
    public static function totalSold(): float
    {
        return (float) static::where('type', self::TYPE_SELL)->sum('amount');
    }

    /** Total silver used by invoices (per invoice: sum(gross) - sum(stone) * InvoiceDetail::SILVER_STOCK_STONE_MULTIPLIER) */
    public static function totalInvoiceUsage(): float
    {
        return (float) static::where('type', self::TYPE_INVOICE_USAGE)->sum('amount');
    }

    /** Current stock = added - sold (before invoice usage) */
    public static function currentStock(): float
    {
        return self::totalAdded() - self::totalSold();
    }

    /** Remaining stock = current stock - invoice usage (can be negative) */
    public static function remainingStock(): float
    {
        return self::currentStock() - self::totalInvoiceUsage();
    }
}
