<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice AD{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @charset "UTF-8";
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 15px;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
        }
        .header-info {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo img {
            height: 48px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-section {
            margin-top: 15px;
            padding: 12px;
            background-color: #f9fafb;
            border-radius: 4px;
            text-align: right;
            width: 100%;
        }
        .totals-row {
            display: block;
            width: 100%;
        }
        .totals-box {
            width: 100%;
            max-width: 400px;
            margin-left: auto;
            text-align: right;
        }
        .totals-item {
            text-align: right;
            margin-bottom: 6px;
            white-space: nowrap;
            line-height: 1.5;
        }
        .totals-item.total {
            border-top: 2px solid #d1d5db;
            padding-top: 8px;
            margin-top: 8px;
        }
        .totals-item.total span {
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }
        .invoice-number {
            color: #6b7280;
        }
        .date-info {
            text-align: right;
        }
    </style>
</head>
<body>
    <!-- Invoice Header -->
    <div class="header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: top; width: 50%; padding-right: 15px;">
                    <p style="margin: 0 0 5px 0; color: #1f2937; font-weight: bold; font-size: 16px;">APPU DIAMONDS PVT LTD</p>
                    <p style="margin: 0 0 3px 0; color: #374151; font-size: 11px; line-height: 1.4;">46, SOUTH AVANI MOOLA STREET</p>
                    <p style="margin: 0 0 3px 0; color: #374151; font-size: 11px; line-height: 1.4;">MADURAI</p>
                    <p style="margin: 0 0 3px 0; color: #374151; font-size: 11px; line-height: 1.4;">GSTIN/UIN : 33AAXCA1852C1ZS</p>
                    <p style="margin: 0 0 3px 0; color: #374151; font-size: 11px; line-height: 1.4;">State Name : Tamil Nadu, Code : 33</p>
                    <p style="margin: 0 0 3px 0; color: #374151; font-size: 11px; line-height: 1.4;">Contact : 82481 61043</p>
                    <p style="margin: 0 0 10px 0; color: #374151; font-size: 11px; line-height: 1.4;">E-Mail : sales@appudiamonds.com</p>
                </td>
                <td style="vertical-align: top; text-align: right; width: 50%; padding-left: 15px;">
                    <h1 style="margin: 0 0 8px 0; padding: 0; font-size: 26px; font-weight: bold; color: #1f2937;">DELIVERY NOTE</h1>
                    <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 11px;">WORKING SHEET #AD{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</p>
                    @if($invoice->customer_name)
                        <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 11px;">Customer Name: <strong>{{ $invoice->customer_name }}</strong></p>
                    @endif
                    @if($invoice->location)
                        <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 11px;">Location: <strong>{{ $invoice->location }}</strong></p>
                    @endif
                    @if($invoice->productType)
                        <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 11px;">Product Type: <strong>{{ $invoice->productType->name }}</strong></p>
                    @endif
                    @if($invoice->total_count)
                        <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 11px;">Total Count: <strong>{{ $invoice->total_count }}</strong></p>
                    @endif
                    @if($invoice->due_date)
                        <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 11px;">Due Date: <strong>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') }}</strong></p>
                    @endif
                    @if($invoice->toggle_silver_cost && $invoice->assignee_name)
                        <p style="margin: 0 0 6px 0; color: #6b7280; font-size: 11px;">Actual Silver Weight: <strong>{{ number_format($invoice->actual_silver_weight ?? 0, 2) }} Gms</strong></p>
                    @endif
                    @if($invoice->toggle_silver_cost && $invoice->assignee_name)
                        <p style="margin: 0; color: #6b7280; font-size: 11px;">Assignee: <strong>{{ $invoice->assignee_name }}</strong></p>
                    @endif
                </td>
            </tr>
        </table>
    </div>

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
        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 11px; text-align: left;">Silver Rate: <strong>₹{{ $silverRateNum !== null ? number_format($silverRateNum, 2) : '—' }}</strong></p>
    @endif
    <!-- Products Table -->
    <table>
        <thead>
            <tr>
                <th>SL No</th>
                <th>Tag name</th>
                <th>Ring Size</th>
                <th>Stone Weight</th>
                <th>Gross Weight</th>
                @if($showSilverRateCol)
                <th class="text-right">Silver Rate (Ex Gst)</th>
                @endif
                <th class="text-right">Stone+Making Charge+Certificate Charge</th>
                @if($showSilverRateCol)
                <th class="text-right">Total Value</th>
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
                <tr>
                    <td>{{ $detail->product_sl_no }}</td>
                    <td>{{ $detail->stoneData ? $detail->stoneData->stone_name : 'N/A' }}</td>
                    <td>{{ $detail->ring_size ?? '-' }}</td>
                    <td>{{ number_format($detail->ston_weight ?? 0, 2) }}</td>
                    <td>{{ number_format($detail->gross_weight ?? 0, 2) }}</td>
                    @if($showSilverRateCol)
                    <td class="text-right">{{ $silverRateExGst }}</td>
                    @endif
                    <td class="text-right">₹{{ number_format($combinedCost, 2) }}</td>
                    @if($showSilverRateCol)
                    <td class="text-right"><strong>₹{{ number_format($lineTotalValue, 2) }}</strong></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f3f4f6; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL</td>
                <td class="text-left">{{ number_format($totalSilverWeight, 2) }} Gms</td>
                @if($showSilverRateCol)
                <td class="text-right" style="color: #6b7280;">{{ $silverRateExGst }}</td>
                @endif
                <td class="text-right">₹{{ number_format($totalStoneCost + (($invoice->wastage_making_certification_cost ?? 0) * $invoice->invoiceDetails->count()), 2) }}</td>
                @if($showSilverRateCol)
                <td class="text-right">₹{{ number_format($grandTotalValue, 2) }}</td>
                @endif
            </tr>
        </tfoot>
    </table>

    <!-- Total Summary -->
    <div class="totals-section">
        <div class="totals-row">
            <div class="totals-box">
                <div class="totals-item">
                    <span>Total Silver Weight: </span>
                    <strong>{{ number_format($totalSilverWeight, 2) }} Gms</strong>
                </div>
                <div class="totals-item">
                    <span>Total Stone+Making Charge+Certificate Charge: </span>
                    <strong>₹{{ number_format($totalStoneCost + (($invoice->wastage_making_certification_cost ?? 0) * $invoice->invoiceDetails->count()), 2) }}</strong>
                </div>
                @unless($showSilverRateCol)
                <div class="totals-item total" style="border-top: 2px solid #d1d5db; padding-top: 8px; margin-top: 8px;">
                    <span style="font-weight: bold;">Due In Silver(Pure): </span>
                    <span style="color: #2563eb; font-weight: bold; font-size: 14px;">{{ number_format($totalSilverWeight, 2) }} Gms</span>
                </div>
                @endunless
                <div class="totals-item total" @if($showSilverRateCol) style="border-top: 2px solid #d1d5db; padding-top: 8px; margin-top: 8px;" @endif>
                    <span style="font-weight: bold;">Due in cash: </span>
                    <span style="color: #2563eb; font-weight: bold; font-size: 14px;">₹{{ number_format($showSilverRateCol ? $grandTotalValue : ($totalStoneCost + (($invoice->wastage_making_certification_cost ?? 0) * $invoice->invoiceDetails->count())), 2) }}</span>
                </div>
                <div style="margin-top: 6px; text-align: right;">
                    <span style="font-size: 11px; color: #6b7280; font-style: italic;">+ Extra GST*</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p style="margin: 0;">GST Extra after Due in Cash with Condition mark.</p>
    </div>
</body>
</html>
