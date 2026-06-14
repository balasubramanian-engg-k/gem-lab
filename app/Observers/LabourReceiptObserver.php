<?php

namespace App\Observers;

use App\Models\LabourReceipt;
use App\Models\LabourReceiptChangelog;
use App\Models\ProductType;

class LabourReceiptObserver
{
    public function created(LabourReceipt $receipt): void
    {
        LabourReceiptChangelog::create([
            'labour_receipt_id' => $receipt->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'description' => 'Labour receipt created',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function updated(LabourReceipt $receipt): void
    {
        $changes = $receipt->getChanges();
        $original = $receipt->getOriginal();

        if (count($changes) === 1 && isset($changes['updated_at'])) {
            return;
        }

        foreach ($changes as $field => $newValue) {
            if (in_array($field, ['updated_at', 'created_at'], true)) {
                continue;
            }

            $oldValue = $original[$field] ?? null;
            $formattedOld = $this->formatValue($oldValue, $field);
            $formattedNew = $this->formatValue($newValue, $field);

            LabourReceiptChangelog::create([
                'labour_receipt_id' => $receipt->id,
                'user_id' => auth()->id(),
                'action' => $field === 'workflow_status' ? 'status_changed' : 'updated',
                'field_name' => $field,
                'old_value' => $formattedOld,
                'new_value' => $formattedNew,
                'field_label' => $this->getFieldLabel($field),
                'description' => $this->getDescription($field, $formattedOld, $formattedNew),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    private function formatValue($value, string $field): string
    {
        if ($value === null) {
            return '-';
        }
        if ($field === 'product_type_id' && $value) {
            $pt = ProductType::find($value);
            return $pt ? $pt->name : (string) $value;
        }
        if (in_array($field, ['silver_gross_weight', 'total_weight_received', 'amount'], true)) {
            return is_numeric($value) ? number_format((float) $value, $field === 'amount' ? 2 : 3) : (string) $value;
        }
        if (in_array($field, ['count_issued', 'total_count_received'], true)) {
            return (string) $value;
        }

        return (string) $value;
    }

    private function getFieldLabel(string $field): string
    {
        $labels = [
            'receipt_number' => 'MC Id',
            'craftman_name' => 'Craftman name',
            'craftman_id' => 'Craftman',
            'product_type_id' => 'Product',
            'count_issued' => 'Count issued',
            'silver_gross_weight' => 'Silver gross weight (g)',
            'amount' => 'Amount',
            'workflow_status' => 'Status',
            'total_count_received' => 'Count received',
            'total_weight_received' => 'Weight received (g)',
            'remarks' => 'Remarks',
            'user_id' => 'Created by',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    private function getDescription(string $field, string $oldValue, string $newValue): string
    {
        $label = $this->getFieldLabel($field);
        if ($field === 'workflow_status') {
            return "Status changed from '{$oldValue}' to '{$newValue}'";
        }

        return "{$label} changed from '{$oldValue}' to '{$newValue}'";
    }
}
