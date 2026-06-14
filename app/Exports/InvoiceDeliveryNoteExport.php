<?php

namespace App\Exports;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

/**
 * Delivery note layout aligned with resources/views/invoices/pdf.blade.php (same figures / columns).
 */
class InvoiceDeliveryNoteExport implements FromCollection
{
    public function __construct(
        protected Invoice $invoice
    ) {
        $this->invoice->load(['invoiceDetails.stoneData', 'productType']);
    }

    public function collection(): Collection
    {
        $invoice = $this->invoice;
        $rows = collect();

        $rows->push(['APPU DIAMONDS PVT LTD']);
        $rows->push(['46, SOUTH AVANI MOOLA STREET']);
        $rows->push(['MADURAI']);
        $rows->push(['GSTIN/UIN : 33AAXCA1852C1ZS']);
        $rows->push(['State Name : Tamil Nadu, Code : 33']);
        $rows->push(['Contact : 82481 61043']);
        $rows->push(['E-Mail : sales@appudiamonds.com']);
        $rows->push(['']);
        $rows->push(['DELIVERY NOTE', 'WORKING SHEET #AD'.str_pad((string) $invoice->id, 6, '0', STR_PAD_LEFT)]);
        if ($invoice->customer_name) {
            $rows->push(['Customer Name:', $invoice->customer_name]);
        }
        if ($invoice->location) {
            $rows->push(['Location:', $invoice->location]);
        }
        if ($invoice->productType) {
            $rows->push(['Product Type:', $invoice->productType->name]);
        }
        if ($invoice->total_count !== null) {
            $rows->push(['Total Count:', (string) $invoice->total_count]);
        }
        if ($invoice->due_date) {
            $rows->push(['Due Date:', Carbon::parse($invoice->due_date)->format('d-m-Y')]);
        }
        if ($invoice->toggle_silver_cost && $invoice->assignee_name) {
            $rows->push(['Actual Silver Weight:', number_format((float) ($invoice->actual_silver_weight ?? 0), 2).' Gms']);
        }
        if ($invoice->toggle_silver_cost && $invoice->assignee_name) {
            $rows->push(['Assignee:', $invoice->assignee_name]);
        }
        $rows->push(['']);

        $showSilverRateCol = $invoice->toggle_silver_rate;
        $silverRateNum = $invoice->silver_rate !== null ? (float) $invoice->silver_rate : null;
        $silverRateExGst = $silverRateNum !== null ? '₹'.number_format($silverRateNum, 2) : '—';

        $totalSilverWeight = 0.0;
        $totalStoneCost = 0.0;
        $grandTotalValue = 0.0;
        foreach ($invoice->invoiceDetails as $detail) {
            $totalSilverWeight += (float) ($detail->gross_weight ?? 0);
            $totalStoneCost += (float) ($detail->stonecost ?? 0);
            $lineCombined = (float) ($detail->stonecost ?? 0) + (float) ($invoice->wastage_making_certification_cost ?? 0);
            $silverLineAmount = ($showSilverRateCol && $silverRateNum !== null)
                ? (float) ($detail->gross_weight ?? 0) * $silverRateNum
                : 0.0;
            $grandTotalValue += $lineCombined + $silverLineAmount;
        }

        if ($showSilverRateCol) {
            $rows->push(['Silver Rate:', '₹'.($silverRateNum !== null ? number_format($silverRateNum, 2) : '—')]);
            $rows->push(['']);
        }

        $headings = ['SL No', 'Tag name', 'Ring Size', 'Stone Weight', 'Gross Weight'];
        if ($showSilverRateCol) {
            $headings[] = 'Silver Rate (Ex Gst)';
        }
        $headings[] = 'Stone+Making Charge+Certificate Charge';
        if ($showSilverRateCol) {
            $headings[] = 'Total Value';
        }
        $rows->push($headings);

        foreach ($invoice->invoiceDetails as $detail) {
            $combinedCost = (float) ($detail->stonecost ?? 0) + (float) ($invoice->wastage_making_certification_cost ?? 0);
            $silverLineAmount = ($showSilverRateCol && $silverRateNum !== null)
                ? (float) ($detail->gross_weight ?? 0) * $silverRateNum
                : 0.0;
            $lineTotalValue = $combinedCost + $silverLineAmount;

            $line = [
                $detail->product_sl_no,
                $detail->stoneData ? $detail->stoneData->stone_name : 'N/A',
                $detail->ring_size ?? '-',
                number_format((float) ($detail->ston_weight ?? 0), 2),
                number_format((float) ($detail->gross_weight ?? 0), 2),
            ];
            if ($showSilverRateCol) {
                $line[] = $silverRateExGst;
            }
            $line[] = '₹'.number_format($combinedCost, 2);
            if ($showSilverRateCol) {
                $line[] = '₹'.number_format($lineTotalValue, 2);
            }
            $rows->push($line);
        }

        $detailCount = $invoice->invoiceDetails->count();
        $totalRow = ['TOTAL', '', '', '', number_format($totalSilverWeight, 2).' Gms'];
        if ($showSilverRateCol) {
            $totalRow[] = $silverRateExGst;
        }
        $totalRow[] = '₹'.number_format($totalStoneCost + ((float) ($invoice->wastage_making_certification_cost ?? 0) * $detailCount), 2);
        if ($showSilverRateCol) {
            $totalRow[] = '₹'.number_format($grandTotalValue, 2);
        }
        $rows->push($totalRow);
        $rows->push(['']);

        $rows->push(['Total Silver Weight:', number_format($totalSilverWeight, 2).' Gms']);
        $rows->push([
            'Total Gem Stone+Making Charge+Certificate Charge:',
            '₹'.number_format($totalStoneCost + ((float) ($invoice->wastage_making_certification_cost ?? 0) * $detailCount), 2),
        ]);
        if (! $showSilverRateCol) {
            $rows->push(['Due In Silver(Pure):', number_format($totalSilverWeight, 2).' Gms']);
        }
        $dueCash = $showSilverRateCol
            ? $grandTotalValue
            : ($totalStoneCost + ((float) ($invoice->wastage_making_certification_cost ?? 0) * $detailCount));
        $rows->push(['Due in cash:', '₹'.number_format($dueCash, 2)]);
        $rows->push(['', '+ Extra GST*']);
        $rows->push(['']);
        $rows->push(['GST Extra after Due in Cash with Condition mark.']);

        return $rows;
    }
}
