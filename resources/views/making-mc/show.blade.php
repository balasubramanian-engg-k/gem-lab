@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Order {{ $receipt->mc_id }}</h1>
            <div class="flex flex-wrap gap-2">
                @if($receipt->workflow_status !== 'FULLY_RECEIVED')
                    <a href="{{ route('making-mc.returns.create', $receipt) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Add return</a>
                @endif
                <a href="{{ route('making-mc.returns.export', $receipt) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export returns to Excel
                </a>
                <a href="{{ route('making-mc.changelog', $receipt) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">Changelog</a>
                <a href="{{ route('making-mc.edit', $receipt) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">Edit</a>
                @if(Auth::check() && Auth::user()->is_admin)
                    <a href="{{ route('making-mc.delete', $receipt) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Delete</a>
                @endif
                <a href="{{ route('making-mc.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Back to list</a>
            </div>
        </div>
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div><dt class="text-sm text-gray-500">Created by</dt><dd class="font-medium">{{ $receipt->creator->name ?? '-' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Craftman</dt><dd class="font-medium">{{ $receipt->craftman_display_name }}</dd></div>
            <div><dt class="text-sm text-gray-500">Product</dt><dd class="font-medium">{{ $receipt->productType->name ?? '-' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Count issued</dt><dd class="font-medium">{{ $receipt->count_issued }}</dd></div>
            <div><dt class="text-sm text-gray-500">Silver gross weight (g)</dt><dd class="font-medium">{{ number_format($receipt->silver_gross_weight, 3) }}</dd></div>
            <div><dt class="text-sm text-gray-500">To Pay</dt><dd class="font-medium">{{ number_format($receipt->amount, 2) }}</dd></div>
            <div><dt class="text-sm text-gray-500">Count received</dt><dd class="font-medium">{{ $receipt->total_count_received }}</dd></div>
            <div><dt class="text-sm text-gray-500">Weight received (g)</dt><dd class="font-medium">{{ number_format((float) $receipt->computed_weight_received, 3) }}</dd></div>
            <div><dt class="text-sm text-gray-500">Status</dt><dd class="font-medium">{{ $receipt->workflow_status }}</dd></div>
            @if($receipt->remarks)
                <div class="md:col-span-2"><dt class="text-sm text-gray-500">Remarks</dt><dd class="font-medium">{{ $receipt->remarks }}</dd></div>
            @endif
        </dl>

        <h2 class="text-lg font-semibold text-gray-800 mb-2">Returns</h2>
        @if($receipt->returns->isEmpty())
            <p class="text-gray-500 text-sm">No returns yet. Use "Add return" to record receipts from the craftman.</p>
        @else
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Date</th>
                        <th class="px-4 py-2 text-right font-medium text-gray-700">Count</th>
                        <th class="px-4 py-2 text-right font-medium text-gray-700">Weight (g)</th>
                        <th class="px-4 py-2 text-right font-medium text-gray-700">Wastage (g)</th>
                        <th class="px-4 py-2 text-right font-medium text-gray-700">Paid</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Remarks</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipt->returns as $ret)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $ret->received_at->format('d-m-Y') }}</td>
                        <td class="px-4 py-2 text-right">{{ $ret->count_received }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($ret->weight_received, 3) }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($ret->wastage_grams ?? 0, 3) }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($ret->amount_paid ?? 0, 2) }}</td>
                        <td class="px-4 py-2">{{ $ret->remarks ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <form action="{{ route('making-mc.returns.destroy', [$receipt, $ret]) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this return?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
