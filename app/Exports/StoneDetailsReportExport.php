<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class StoneDetailsReportExport implements FromCollection
{
    public function __construct(
        protected array $payload
    ) {}

    public function collection(): Collection
    {
        $columns = $this->payload['columns'] ?? [];
        $stoneRows = $this->payload['stoneRows'] ?? [];
        $multiCustomer = $this->payload['multiCustomer'] ?? false;
        $productTypeName = $this->payload['productTypeName'] ?? null;
        $customerName = $this->payload['customerName'] ?? null;

        $rows = collect();

        $title = 'Stone Details Report';
        if ($multiCustomer) {
            $title .= ' (All Customers)';
        } elseif ($customerName) {
            $title .= ' — ' . $customerName;
        }
        $rows->push([$title]);

        if (! empty($productTypeName)) {
            $rows->push(['Product Type:', $productTypeName]);
        }
        if (! empty($customerName) && ! $multiCustomer) {
            $rows->push(['Customer:', $customerName]);
        }

        $rows->push(['']);

        if (count($columns) === 0) {
            $rows->push(['No data found for the selected filters.']);

            return $rows;
        }

        $header = ['STONE DETAILS'];
        foreach ($columns as $col) {
            $inv = implode(', ', $col['invoiceNumbers'] ?? []);
            $header[] = $col['name'].($inv !== '' ? ' — '.$inv : '');
        }
        $rows->push($header);

        foreach ($stoneRows as $stoneName) {
            $row = [$stoneName];
            foreach ($columns as $col) {
                $sizes = $col['byStone'][$stoneName] ?? [];
                $row[] = is_array($sizes) ? implode(', ', $sizes) : (string) $sizes;
            }
            $rows->push($row);
        }

        $totalRow = ['TOTAL PCS'];
        foreach ($columns as $col) {
            $totalRow[] = $col['totalPcs'] ?? 0;
        }
        $rows->push($totalRow);

        return $rows;
    }
}
