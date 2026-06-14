@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6 flex-wrap gap-2">
            <h1 class="text-2xl font-bold text-gray-800">Craftman Ledger – {{ $craftmanName }}</h1>
            <a href="{{ $pdfUrl }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Download PDF</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Sno</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Receipt ID</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Craftman Name</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Product Name</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Count Issued</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Count Received</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Gross weight - Issued</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-700">Gross weight - Received</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $sno => $r)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $sno + 1 }}</td>
                        <td class="px-4 py-2 font-medium">{{ $r->mc_id }}</td>
                        <td class="px-4 py-2">{{ $r->craftman_display_name }}</td>
                        <td class="px-4 py-2">{{ $r->productType->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-right">{{ $r->count_issued }}</td>
                        <td class="px-4 py-2 text-right">{{ $r->total_count_received }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format((float) $r->computed_weight_received, 3) }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format((float) $r->computed_weight_received, 3) }}</td>
                    </tr>
                    @endforeach
                    <tr class="border-b bg-gray-100 font-semibold">
                        <td class="px-4 py-2" colspan="4">Total</td>
                        <td class="px-4 py-2 text-right">{{ $totalIssued }}</td>
                        <td class="px-4 py-2 text-right">{{ $totalReceived }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($totalWeightIssued, 3) }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($totalWeightReceived, 3) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @if($rows->isEmpty())
            <p class="mt-4 text-gray-500">No orders found for this craftman in the selected period.</p>
        @endif
    </div>
</div>
@endsection
