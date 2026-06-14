<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabourReceipt extends Model
{
    public const STATUS_ISSUED = 'ISSUED';
    public const STATUS_PARTIALLY_RECEIVED = 'PARTIALLY_RECEIVED';
    public const STATUS_FULLY_RECEIVED = 'FULLY_RECEIVED';

    protected $fillable = [
        'user_id',
        'receipt_number',
        'craftman_id',
        'craftman_name',
        'product_type_id',
        'count_issued',
        'silver_gross_weight',
        'amount',
        'workflow_status',
        'total_count_received',
        'total_weight_received',
        'remarks',
    ];

    protected $casts = [
        'silver_gross_weight' => 'decimal:3',
        'amount' => 'decimal:2',
        'total_weight_received' => 'decimal:3',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function changelogs(): HasMany
    {
        return $this->hasMany(LabourReceiptChangelog::class)->orderByDesc('created_at');
    }

    public function craftman(): BelongsTo
    {
        return $this->belongsTo(Craftman::class, 'craftman_id');
    }

    /** Display name: free-text craftman name or linked craftman */
    public function getCraftmanDisplayNameAttribute(): string
    {
        if ($this->craftman_name !== null && $this->craftman_name !== '') {
            return $this->craftman_name;
        }
        return $this->craftman->name ?? '-';
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(LabourReceiptReturn::class, 'labour_receipt_id');
    }

    /**
     * Display-only weight received: ornaments + wastage.
     * Calculated after fetching using loaded sums when available.
     */
    public function getComputedWeightReceivedAttribute(): float
    {
        $ornament = (float) ($this->ornament_weight_received ?? $this->total_weight_received ?? 0);
        $wastage = (float) ($this->wastage_weight_received ?? 0);
        return $ornament + $wastage;
    }

    /** MC Id display e.g. ADCR001 */
    public function getMcIdAttribute(): string
    {
        return $this->receipt_number ?: ('ADCR' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT));
    }

    /** Update totals and workflow status after a return is added */
    public function refreshTotalsAndStatus(): void
    {
        $totalCount = $this->returns()->sum('count_received');
        // Persist ornament weight only; full return weight vs issued uses wastage too.
        $totalWeight = (float) $this->returns()->sum('weight_received');
        $wastageWeight = (float) $this->returns()->sum('wastage_grams');
        $combinedReceivedWeight = $totalWeight + $wastageWeight;

        $issuedCount = (int) $this->count_issued;
        $receivedCount = (int) $totalCount;
        $countMatched = ($issuedCount > 0 && $receivedCount === $issuedCount);

        $issuedWeight = (float) $this->silver_gross_weight;
        // Match issued silver gross to sum(weight_received + wastage_grams); small epsilon for decimals.
        $weightMatched = abs($combinedReceivedWeight - $issuedWeight) < 0.001;

        $status = self::STATUS_ISSUED;
        if ($countMatched && $weightMatched) {
            $status = self::STATUS_FULLY_RECEIVED;
        } elseif ($totalCount > 0 || $combinedReceivedWeight > 0) {
            $status = self::STATUS_PARTIALLY_RECEIVED;
        }

        $this->update([
            'total_count_received' => $totalCount,
            'total_weight_received' => $totalWeight,
            'workflow_status' => $status,
        ]);
    }
}
