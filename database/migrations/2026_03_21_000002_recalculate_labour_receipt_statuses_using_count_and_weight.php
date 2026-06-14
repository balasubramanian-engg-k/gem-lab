<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('labour_receipts')
            ->select('id', 'count_issued', 'silver_gross_weight')
            ->orderBy('id')
            ->chunkById(500, function ($receipts): void {
                $receiptIds = $receipts->pluck('id')->all();

                $returnsByReceipt = DB::table('labour_receipt_returns')
                    ->selectRaw('labour_receipt_id, COALESCE(SUM(count_received), 0) as total_count, COALESCE(SUM(weight_received), 0) as total_weight, COALESCE(SUM(wastage_grams), 0) as total_wastage')
                    ->whereIn('labour_receipt_id', $receiptIds)
                    ->groupBy('labour_receipt_id')
                    ->get()
                    ->keyBy('labour_receipt_id');

                $now = now();

                foreach ($receipts as $receipt) {
                    $agg = $returnsByReceipt->get($receipt->id);

                    $totalCount = (int) ($agg->total_count ?? 0);
                    $totalWeight = (float) ($agg->total_weight ?? 0);
                    $wastageWeight = (float) ($agg->total_wastage ?? 0);
                    $combinedReceivedWeight = $totalWeight + $wastageWeight;

                    $issuedCount = (int) $receipt->count_issued;
                    $receivedCount = (int) $totalCount;
                    $countMatched = ($issuedCount > 0 && $receivedCount === $issuedCount);

                    $issuedWeight = (float) $receipt->silver_gross_weight;
                    $weightMatched = abs($combinedReceivedWeight - $issuedWeight) < 0.001;

                    $status = 'ISSUED';
                    if ($countMatched && $weightMatched) {
                        $status = 'FULLY_RECEIVED';
                    } elseif ($totalCount > 0 || $combinedReceivedWeight > 0) {
                        $status = 'PARTIALLY_RECEIVED';
                    }

                    DB::table('labour_receipts')
                        ->where('id', $receipt->id)
                        ->update([
                            'total_count_received' => $totalCount,
                            'total_weight_received' => $totalWeight,
                            'workflow_status' => $status,
                            'updated_at' => $now,
                        ]);
                }
            });
    }

    public function down(): void
    {
        // No-op: this migration recalculates existing data only.
    }
};

