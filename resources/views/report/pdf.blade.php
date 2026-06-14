<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Stone Details Report {{ $multiCustomer ? '- All Customers' : '- ' . $customerName }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            padding: 12px;
            line-height: 1.25;
        }
        h1 {
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }
        tr { page-break-inside: avoid; }
        th, td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #e5e7eb;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f3f4f6;
        }
        .stone-cell { min-width: 100px; }
        .qty-cell { text-align: right; }
    </style>
</head>
<body>
    <h1>Stone Details Report {{ $multiCustomer ? '(All Customers)' : '' }}</h1>
    @if(!empty($productTypeName))
        <p class="report-meta" style="margin-bottom: 4px; font-size: 11px;"><strong>Product Type:</strong> {{ $productTypeName }}</p>
    @endif
    @if(!empty($customerName))
        <p class="report-meta" style="margin-bottom: 10px; font-size: 11px;"><strong>Customer:</strong> {{ $customerName }}</p>
    @endif

    @if(count($columns) > 0)
        <table>
            <thead>
                <tr>
                    <th class="stone-cell">STONE DETAILS</th>
                    @foreach($columns as $col)
                        <th>{{ $col['name'] }}<br><span style="font-size: 9px; font-weight: normal;">{{ implode(', ', $col['invoiceNumbers'] ?? []) }}</span></th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($stoneRows as $stoneName)
                <tr>
                    <td>{{ $stoneName }}</td>
                    @foreach($columns as $col)
                        <td>{{ implode(', ', $col['byStone'][$stoneName] ?? []) }}</td>
                    @endforeach
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>TOTAL PCS</td>
                    @foreach($columns as $col)
                        <td class="qty-cell">{{ $col['totalPcs'] }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @else
        <p style="margin-top: 10px;">No data found for the selected filters.</p>
    @endif
</body>
</html>
