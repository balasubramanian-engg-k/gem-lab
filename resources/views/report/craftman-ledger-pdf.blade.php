<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Craftman Ledger – {{ $craftmanName }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #e5e7eb; font-weight: bold; }
        .total-row { font-weight: bold; background-color: #f3f4f6; }
        .num { text-align: right; }
    </style>
</head>
<body>
    <h1>Craftman Ledger Report – {{ $craftmanName }}</h1>
    <table>
        <thead>
            <tr>
                <th>Sno</th>
                <th>Receipt ID</th>
                <th>Craftman Name</th>
                <th>Product Name</th>
                <th class="num">Count Issued</th>
                <th class="num">Count Received</th>
                <th class="num">Gross weight - Issued</th>
                <th class="num">Gross weight - Received</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $sno => $r)
            <tr>
                <td>{{ $sno + 1 }}</td>
                <td>{{ $r->mc_id }}</td>
                <td>{{ $r->craftman_display_name }}</td>
                <td>{{ $r->productType->name ?? '-' }}</td>
                <td class="num">{{ $r->count_issued }}</td>
                <td class="num">{{ $r->total_count_received }}</td>
                <td class="num">{{ number_format((float) $r->computed_weight_received, 3) }}</td>
                <td class="num">{{ number_format((float) $r->computed_weight_received, 3) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4">Total</td>
                <td class="num">{{ $totalCountIssued }}</td>
                <td class="num">{{ $totalCountReceived }}</td>
                <td class="num">{{ number_format($totalWeightIssued, 3) }}</td>
                <td class="num">{{ number_format($totalWeightReceived, 3) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
