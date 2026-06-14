<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LabourReceiptsExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected $receipts
    ) {}

    public function collection()
    {
        $rows = $this->receipts->map(function ($r) {
            $status = $r->workflow_status === 'FULLY_RECEIVED' ? 'Fully Received'
                : ($r->workflow_status === 'PARTIALLY_RECEIVED' ? 'Partially Received' : 'Issued');
            return [
                $r->mc_id,
                $r->craftman_display_name,
                $r->productType->name ?? '-',
                $r->count_issued,
                $r->total_count_received,
                number_format((float) $r->silver_gross_weight, 3),
                number_format((float) $r->computed_weight_received, 3),
                number_format($r->amount, 2),
                number_format((float) ($r->paid_total ?? 0), 2),
                $status,
                $r->created_at->format('d-m-Y'),
                $r->creator->name ?? '-',
            ];
        });

        $rows->push([
            'Grand Total',
            '',
            '',
            (int) $this->receipts->sum('count_issued'),
            (int) $this->receipts->sum('total_count_received'),
            number_format((float) $this->receipts->sum(fn ($r) => (float) $r->silver_gross_weight), 3),
            number_format((float) $this->receipts->sum(fn ($r) => (float) $r->computed_weight_received), 3),
            number_format((float) $this->receipts->sum('amount'), 2),
            number_format((float) $this->receipts->sum(fn ($r) => (float) ($r->paid_total ?? 0)), 2),
            '',
            '',
            '',
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'MC Id',
            'Craftman',
            'Product',
            'Count issued',
            'Count received',
            'Weight issued',
            'Weight received',
            'Chargable Amount',
            'Paid Amount',
            'Status',
            'Date',
            'Created by',
        ];
    }
}
