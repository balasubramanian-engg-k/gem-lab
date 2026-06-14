<div class="bg-white rounded-lg shadow-lg p-8 max-w-5xl mx-auto" id="invoicePrint">
    <!-- Print and Download Buttons -->
    <div class="mb-4 text-right print:hidden flex justify-end gap-3">
        <a href="{{ route('invoices.downloadPdf', $invoice->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition inline-flex items-center gap-2">
            📥 Download PDF
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            🖨 Print Invoice
        </button>
    </div>

    <!-- Invoice Header -->
    <div class="border-b-2 border-gray-300 pb-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-800 font-bold text-lg mb-1">APPU DIAMONDS PVT LTD</p>
                <p class="text-gray-700 text-sm mb-1">46, SOUTH AVANI MOOLA STREET</p>
                <p class="text-gray-700 text-sm mb-1">MADURAI</p>
                <p class="text-gray-700 text-sm mb-1">GSTIN/UIN : 33AAXCA1852C1ZS</p>
                <p class="text-gray-700 text-sm mb-1">State Name : Tamil Nadu, Code : 33</p>
                <p class="text-gray-700 text-sm mb-1">Contact : 82481 61043</p>
                <p class="text-gray-700 text-sm mb-2">E-Mail : sales@appudiamonds.com</p>
            </div>
            <div class="text-right" style="margin-top: 0;">
                <h1 class="text-3xl font-bold text-gray-800 mb-2" style="margin-top: 0;">DELIVERY NOTE</h1>
                <p class="text-gray-600 mb-2">WORKING SHEET #AD{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</p>
                @if($invoice->customer_name)
                    <p class="text-gray-600 mb-2">Customer Name: <span class="font-semibold">{{ $invoice->customer_name }}</span></p>
                @endif
                @if($invoice->status)
                    <p class="text-gray-600 mb-2">Location: <span class="font-semibold">{{ $invoice->location }}</span></p>
                @endif
                @if($invoice->productType)
                    <p class="text-gray-600 mb-2">Product Type: <span class="font-semibold">{{ $invoice->productType->name }}</span></p>
                @endif
                @if($invoice->total_count)
                    <p class="text-gray-600 mb-2">Total Count: <span class="font-semibold">{{ $invoice->total_count }}</span></p>
                @endif
                @if($invoice->due_date)
                    <p class="text-gray-600 mb-2">Due Date: <span class="font-semibold">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') }}</span></p>
                @endif
                @if($invoice->toggle_silver_cost && $invoice->assignee_name)
                    <p class="text-gray-600 mb-2">Actual Silver Weight: <span class="font-semibold">{{ number_format($invoice->actual_silver_weight ?? 0, 2) }} Gms</span></p>
                @endif
                @if($invoice->toggle_silver_cost && $invoice->assignee_name)
                    <p class="text-gray-600">Assignee: <span class="font-semibold">{{ $invoice->assignee_name }}</span></p>
                @endif
             
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="mb-6">
        @php
            $showSilverRateCol = $invoice->toggle_silver_rate;
            $silverRateNum = $invoice->silver_rate !== null ? (float) $invoice->silver_rate : null;
            $silverRateExGst = $silverRateNum !== null ? '₹' . number_format($silverRateNum, 2) : '—';
            $totalSilverWeight = 0;
            $totalStoneCost = 0;
            $grandTotalValue = 0;
            foreach($invoice->invoiceDetails as $detail) {
                $totalSilverWeight += $detail->gross_weight ?? 0;
                $totalStoneCost += $detail->stonecost ?? 0;
                $lineCombined = ($detail->stonecost ?? 0) + ($invoice->wastage_making_certification_cost ?? 0);
                $silverLineAmount = ($showSilverRateCol && $silverRateNum !== null)
                    ? (float) ($detail->gross_weight ?? 0) * $silverRateNum
                    : 0;
                $grandTotalValue += $lineCombined + $silverLineAmount;
            }
        @endphp
        @if($showSilverRateCol)
        <p class="font-semibold" style="margin: 0 0 10px 0; color: #6b7280;  text-align: left;">Silver Rate: <strong>₹{{ number_format((float) $invoice->silver_rate, 2) }}</strong></p>
        @endif
        <div class="overflow-y-auto max-h-96 border border-gray-300">
            <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">SL No</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">Tag name</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">Ring Size</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">Stone Weight</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-semibold text-gray-700">Gross Weight</th>
                    @if($showSilverRateCol)
                    <th class="border border-gray-300 px-4 py-3 text-right text-sm font-semibold text-gray-700">Silver Rate (Ex Gst)</th>
                    @endif
                    <th class="border border-gray-300 px-4 py-3 text-right text-sm font-semibold text-gray-700">Stone+Making Charge+Certificate Charge</th>
                    @if($showSilverRateCol)
                    <th class="border border-gray-300 px-4 py-3 text-right text-sm font-semibold text-gray-700">Total Value</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoiceDetails as $detail)
                    @php
                        $combinedCost = ($detail->stonecost ?? 0) + ($invoice->wastage_making_certification_cost ?? 0);
                        $silverLineAmount = ($showSilverRateCol && $silverRateNum !== null)
                            ? (float) ($detail->gross_weight ?? 0) * $silverRateNum
                            : 0;
                        $lineTotalValue = $combinedCost + $silverLineAmount;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">{{ $detail->product_sl_no }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">{{ $detail->stoneData ? $detail->stoneData->stone_name : 'N/A' }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">{{ $detail->ring_size ?? '-' }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">{{ number_format($detail->ston_weight ?? 0, 2) }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700">{{ number_format($detail->gross_weight ?? 0, 2) }}</td>
                        @if($showSilverRateCol)
                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700 text-right tabular-nums">{{ $silverRateExGst }}</td>
                        @endif
                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-700 text-right">₹{{ number_format($combinedCost, 2) }}</td>
                        @if($showSilverRateCol)
                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-800 text-right font-medium tabular-nums">₹{{ number_format($lineTotalValue, 2) }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-100 font-semibold">
                <tr>
                    <td colspan="4" class="border border-gray-300 px-4 py-3 text-right text-sm text-gray-700">TOTAL</td>
                    <td class="border border-gray-300 px-4 py-3 text-left text-sm text-gray-800">{{ number_format($totalSilverWeight, 2) }} Gms</td>
                    @if($showSilverRateCol)
                    <td class="border border-gray-300 px-4 py-3 text-right text-sm text-gray-500">{{ $silverRateExGst }}</td>
                    @endif
                    <td class="border border-gray-300 px-4 py-3 text-right text-sm text-gray-800">₹{{ number_format($totalStoneCost + (($invoice->wastage_making_certification_cost ?? 0) * $invoice->invoiceDetails->count()), 2) }}</td>
                    @if($showSilverRateCol)
                    <td class="border border-gray-300 px-4 py-3 text-right text-sm text-gray-900">₹{{ number_format($grandTotalValue, 2) }}</td>
                    @endif
                </tr>
            </tfoot>
        </table>
        </div>
    </div>

    <!-- Total Summary -->
    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
        <div class="flex justify-end">
            <div class="w-full max-w-md">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-700 font-medium">Total Silver Weight: </span>
                    <span class="text-gray-800 font-semibold">{{ number_format($totalSilverWeight, 2) }} Gms</span>
                </div>
                <div class="flex justify-between mb-2 whitespace-nowrap">
                    <span class="text-gray-700 font-medium">Total Gem Stone+Making Charge+Certificate Charge: </span>
                    <span class="text-gray-800 font-semibold ml-2">₹{{ number_format($totalStoneCost + (($invoice->wastage_making_certification_cost ?? 0) * $invoice->invoiceDetails->count()), 2) }}</span>
                </div>
                <div class="border-t-2 border-gray-300 pt-2 mt-2">
                    @unless($showSilverRateCol)
                    <div class="flex justify-between mb-2">
                        <span class="text-lg font-bold text-gray-900">Due In Silver(Pure):</span>
                        <span class="text-xl font-bold text-blue-600">{{ number_format($totalSilverWeight, 2) }} Gms</span>
                    </div>
                    @endunless
                    <div class="flex justify-between {{ $showSilverRateCol ? 'pt-1' : '' }}">
                        <span class="text-lg font-bold text-gray-900">Due in cash:</span>
                        <span class="text-xl font-bold text-blue-600">₹{{ number_format($showSilverRateCol ? $grandTotalValue : ($totalStoneCost + (($invoice->wastage_making_certification_cost ?? 0) * $invoice->invoiceDetails->count())), 2) }}</span>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-lg font-bold text-gray-900">&nbsp;</span>
                        <span class="text-sm text-black-400">+ Extra GST*</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-8 pt-6 border-t border-gray-300 text-center text-sm text-gray-600">
        <p>GST Extra after Due in Cash with Condition mark.</p>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #invoicePrint, #invoicePrint * {
        visibility: visible;
    }
    #invoicePrint {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 20px;
    }
    .print\\:hidden {
        display: none;
    }
    table {
        page-break-inside: auto;
    }
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    thead {
        display: table-header-group;
    }
    tfoot {
        display: table-footer-group;
    }
}
</style>
