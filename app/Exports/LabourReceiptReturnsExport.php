<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LabourReceiptReturnsExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected $returns,
        protected string $orderId
    ) {}

    public function collection()
    {
        return $this->returns->map(function ($ret) {
            return [
                $this->orderId,
                $ret->received_at->format('d-m-Y'),
                $ret->count_received,
                number_format($ret->weight_received, 3),
                number_format($ret->wastage_grams ?? 0, 3),
                number_format($ret->amount_paid ?? 0, 2),
                $ret->remarks ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Date',
            'Count',
            'Weight (g)',
            'Wastage (g)',
            'Paid',
            'Remarks',
        ];
    }
}
