<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\InvoiceChangelog;
use App\Models\SilverStockTransaction;

class InvoiceObserver
{
    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        InvoiceChangelog::create([
            'invoice_id' => $invoice->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'description' => 'Invoice created',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Silver stock: sync runs in InvoiceController::store after line items are saved (created fires before details exist).
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        $changes = $invoice->getChanges();
        $original = $invoice->getOriginal();
        
        // Skip if only updated_at changed
        if (count($changes) === 1 && isset($changes['updated_at'])) {
            return;
        }
        
        foreach ($changes as $field => $newValue) {
            // Skip timestamps
            if (in_array($field, ['updated_at', 'created_at'])) {
                continue;
            }
            
            $oldValue = $original[$field] ?? null;
            
            // Format values for display
            $formattedOldValue = $this->formatValue($oldValue, $field);
            $formattedNewValue = $this->formatValue($newValue, $field);
            
            InvoiceChangelog::create([
                'invoice_id' => $invoice->id,
                'user_id' => auth()->id(),
                'action' => $field === 'status' ? 'status_changed' : 'updated',
                'field_name' => $field,
                'old_value' => $formattedOldValue,
                'new_value' => $formattedNewValue,
                'field_label' => $this->getFieldLabel($field),
                'description' => $this->getDescription($field, $formattedOldValue, $formattedNewValue),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Silver stock: full sync runs in InvoiceController::store/update after line items (updated fires before details are replaced).
        // Status-only updates (e.g. cancel) still sync here so usage is correct without resaving lines.
        $changeKeys = array_keys($changes);
        $nonTs = array_values(array_diff($changeKeys, ['updated_at', 'created_at']));
        if (count($nonTs) === 1 && $nonTs[0] === 'status') {
            $invoice->syncSilverStockUsage();
        }
    }

    /**
     * Handle the Invoice "deleted" event.
     * Note: We do not log deletions to invoice_changelogs because the invoice is already
     * removed from the database, so a foreign key to invoices would fail.
     */
    public function deleted(Invoice $invoice): void
    {
        // Cannot insert changelog for deleted invoice - FK constraint would fail
        SilverStockTransaction::where('invoice_id', $invoice->id)
            ->where('type', SilverStockTransaction::TYPE_INVOICE_USAGE)
            ->delete();
    }

    /**
     * Format value based on field type.
     */
    private function formatValue($value, $field)
    {
        if ($value === null) {
            return '-';
        }

        // Format dates
        if (in_array($field, ['delivered_date', 'due_date', 'created_at', 'updated_at']) && $value) {
            try {
                return \Carbon\Carbon::parse($value)->format('d-m-Y');
            } catch (\Exception $e) {
                return $value;
            }
        }

        // Format product type
        if ($field === 'product_type_id' && $value) {
            $productType = \App\Models\ProductType::find($value);
            return $productType ? $productType->name : $value;
        }

        // Format boolean
        if (in_array($field, ['toggle_silver_cost', 'toggle_silver_rate'], true)) {
            return $value ? 'Enabled' : 'Disabled';
        }

        // Format numbers
        if (in_array($field, ['total_count', 'actual_silver_weight', 'stone_cost', 'wastage_making_certification_cost', 'silver_rate'])) {
            return is_numeric($value) ? number_format($value, 2) : $value;
        }

        return $value;
    }

    /**
     * Get human-readable field label.
     */
    private function getFieldLabel($field)
    {
        $labels = [
            'customer_name' => 'Customer Name',
            'location' => 'Location',
            'status' => 'Status',
            'delivered_date' => 'Delivered Date',
            'total_count' => 'Total Count',
            'actual_silver_weight' => 'Actual Silver Weight',
            'remarks' => 'Remarks',
            'due_date' => 'Due Date',
            'silver_rate' => 'Silver Rate',
            'assignee_name' => 'Assignee',
            'stone_cost' => 'Stone Cost',
            'wastage_making_certification_cost' => 'Wastage+Making+Certification Cost',
            'product_type_id' => 'Product Type',
            'toggle_silver_cost' => 'Toggle Silver Cost',
            'toggle_silver_rate' => 'Toggle Silver Rate',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * Generate description for the change.
     */
    private function getDescription($field, $oldValue, $newValue)
    {
        $label = $this->getFieldLabel($field);
        
        if ($field === 'status') {
            return "Status changed from '{$oldValue}' to '{$newValue}'";
        }
        
        return "{$label} changed from '{$oldValue}' to '{$newValue}'";
    }
}
