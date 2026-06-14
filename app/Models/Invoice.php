<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /** Invoice statuses in workflow order */
    public const STATUSES = ['NEW', 'OPENED', 'SETTING', 'PACKAGING', 'COMPLETED', 'DELIVERED', 'CANCELLED', 'PAID'];

    protected $fillable = [
        'customer_name',
        'location',
        'status',
        'delivered_date',
        'total_count',
        'actual_silver_weight',
        'remarks',
        'due_date',
        'silver_rate',
        'assignee_name',
        'stone_cost',
        'wastage_making_certification_cost',
        'product_type_id',
        'toggle_silver_cost',
        'toggle_silver_rate',
    ];

    protected $casts = [
        'toggle_silver_cost' => 'boolean',
        'toggle_silver_rate' => 'boolean',
    ];

    /**
     * Get the invoice details for this invoice.
     */
    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    /**
     * Get the product type for this invoice.
     */
    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * Get the changelogs for this invoice.
     */
    public function changelogs()
    {
        return $this->hasMany(InvoiceChangelog::class);
    }

    /**
     * Silver stock usage transaction for this invoice (if any).
     */
    public function silverStockUsage()
    {
        return $this->hasOne(SilverStockTransaction::class)->where('type', SilverStockTransaction::TYPE_INVOICE_USAGE);
    }

    /**
     * Persist silver stock usage from current line items. Call after invoice_details exist (not from Invoice::created).
     * Amount = SUM(gross_weight) - SUM(ston_weight) * InvoiceDetail::SILVER_STOCK_STONE_MULTIPLIER.
     * Removes usage when status is CANCELLED.
     */
    public function syncSilverStockUsage(): void
    {
        if (strtoupper((string) $this->status) === 'CANCELLED') {
            SilverStockTransaction::where('invoice_id', $this->id)
                ->where('type', SilverStockTransaction::TYPE_INVOICE_USAGE)
                ->delete();

            return;
        }

        $totals = $this->invoiceDetails()
            ->selectRaw('COALESCE(SUM(gross_weight), 0) as gross_sum, COALESCE(SUM(ston_weight), 0) as stone_sum')
            ->first();

        $sumGross = (float) ($totals?->gross_sum ?? 0);
        $sumStone = (float) ($totals?->stone_sum ?? 0);
        $amount = $sumGross - ($sumStone * InvoiceDetail::SILVER_STOCK_STONE_MULTIPLIER);

        SilverStockTransaction::updateOrCreate(
            [
                'invoice_id' => $this->id,
                'type' => SilverStockTransaction::TYPE_INVOICE_USAGE,
            ],
            [
                'amount' => $amount,
                'transaction_date' => now()->toDateString(),
                'user_id' => auth()->id(),
            ]
        );
    }
}
