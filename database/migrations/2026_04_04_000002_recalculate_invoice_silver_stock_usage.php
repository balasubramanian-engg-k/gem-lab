<?php

use App\Models\InvoiceDetail;
use App\Models\SilverStockTransaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfill silver_stock_transactions (invoice_usage) using SQL only — no Eloquent sync.
 *
 * Scope:
 * - DELETE existing invoice_usage rows whose invoice is CANCELLED.
 * - UPDATE existing invoice_usage rows only (no INSERT for invoices missing a row).
 *
 * Manual run (MySQL / MariaDB — replace :multiplier and adjust dates if needed):
 *
 *   DELETE FROM silver_stock_transactions
 *   WHERE type = 'invoice_usage'
 *     AND invoice_id IN (SELECT id FROM invoices WHERE status = 'CANCELLED');
 *
 *   UPDATE silver_stock_transactions s
 *   SET
 *     amount = (
 *       SELECT COALESCE(SUM(COALESCE(d.gross_weight, 0)), 0) - 0.2 * COALESCE(SUM(COALESCE(d.ston_weight, 0)), 0)
 *       FROM invoice_details d
 *       WHERE d.invoice_id = s.invoice_id
 *     ),
 *     updated_at = NOW()
 *   WHERE s.type = 'invoice_usage'
 *     AND s.invoice_id IN (SELECT id FROM invoices WHERE status != 'CANCELLED');
 *
 * (0.2 = InvoiceDetail::SILVER_STOCK_STONE_MULTIPLIER)
 */
return new class extends Migration
{
    public function up(): void
    {
        $type = SilverStockTransaction::TYPE_INVOICE_USAGE;
        $m = (float) InvoiceDetail::SILVER_STOCK_STONE_MULTIPLIER;
        $now = now()->format('Y-m-d H:i:s');

        DB::transaction(function () use ($type, $m, $now) {
            DB::delete(
                'DELETE FROM silver_stock_transactions WHERE type = ? AND invoice_id IN (SELECT id FROM invoices WHERE status = ?)',
                [$type, 'CANCELLED']
            );

            // Update only rows that already exist; correlated subquery sums current line items.
            DB::update(
                'UPDATE silver_stock_transactions SET
                    amount = (
                        SELECT COALESCE(SUM(COALESCE(d.gross_weight, 0)), 0) - ? * COALESCE(SUM(COALESCE(d.ston_weight, 0)), 0)
                        FROM invoice_details d
                        WHERE d.invoice_id = silver_stock_transactions.invoice_id
                    ),
                    updated_at = ?
                WHERE type = ?
                AND invoice_id IN (SELECT id FROM invoices WHERE status != ?)',
                [$m, $now, $type, 'CANCELLED']
            );
        });
    }

    public function down(): void
    {
        // Not reversible: prior amounts are unknown.
    }
};
