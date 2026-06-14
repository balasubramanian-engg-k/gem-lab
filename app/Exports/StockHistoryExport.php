<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockHistoryExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected $transactions
    ) {}

    public function collection()
    {
        return $this->transactions->map(function ($t) {
            $typeLabel = match ($t->type) {
                'add' => 'Add stock',
                'sell' => 'Sell',
                'vault_update' => 'Vault update',
                default => 'Invoice usage',
            };
            $invoiceLabel = $t->invoice_id ? 'AD' . str_pad($t->invoice_id, 6, '0', STR_PAD_LEFT) : '-';
            return [
                $t->transaction_date->format('d-m-Y'),
                $typeLabel,
                round($t->amount, 3),
                $t->remarks ?? '-',
                $invoiceLabel,
                $t->invoice?->customer_name ?? '-',
                $t->invoice?->location ?? '-',
                $t->user?->name ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type',
            'Weight(gms)',
            'Remarks',
            'Invoice',
            'Customer',
            'Location',
            'User',
        ];
    }
}
