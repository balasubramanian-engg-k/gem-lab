<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoicesExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected $invoices
    ) {}

    public function collection()
    {
        return $this->invoices->map(function ($invoice) {
            return [
                'AD' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT),
                $invoice->customer_name ?? '-',
                $invoice->location ?? '-',
                $invoice->productType->name ?? '-',
                $invoice->total_count ?? '-',
                $invoice->assignee_name ?? '-',
                $invoice->invoice_details_count ?? $invoice->invoiceDetails->count(),
                $invoice->created_at ? Carbon::parse($invoice->created_at)->format('d-m-Y') : '-',
                $invoice->status ?? '-',
                $invoice->delivered_date ? Carbon::parse($invoice->delivered_date)->format('d-m-Y') : '-',
                $invoice->due_date ? Carbon::parse($invoice->due_date)->format('d-m-Y') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Invoice Id',
            'Customer Name',
            'Location',
            'Product Type',
            'Total Count',
            'Assignee',
            'Product Count',
            'Created Date',
            'Status',
            'Delivered On',
            'Due Date',
        ];
    }
}
