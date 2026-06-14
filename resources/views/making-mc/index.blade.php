@extends('layouts.app')

@section('content')
@once
<style>
    details.return-details-block:not([open]) .return-details-hint-open { display: none; }
    details.return-details-block[open] .return-details-hint-closed { display: none; }
    details.return-details-block[open] .return-details-chevron { transform: rotate(90deg); }
</style>
@endonce
<div class="max-w-8xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6 flex-wrap gap-2">
            <h1 class="text-2xl font-bold text-gray-800">Labour Report</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('making-mc.export', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export to Excel
                </a>
                <a href="{{ route('making-mc.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">Create Labour receipt</a>
            </div>
        </div>
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <form method="GET" action="{{ route('making-mc.index') }}" class="mb-4 flex flex-wrap gap-4 items-end">
            <div>
                <label for="craftman_name" class="block text-sm font-medium text-gray-700 mb-1">Craftman name</label>
                <input type="text" name="craftman_name" id="craftman_name" value="{{ request('craftman_name') }}" placeholder="Filter by name" class="border border-gray-300 rounded-lg px-4 py-2">
            </div>
            <div>
                <label for="workflow_status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="workflow_status" id="workflow_status" class="border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">All</option>
                    <option value="ISSUED" {{ request('workflow_status') === 'ISSUED' ? 'selected' : '' }}>Issued</option>
                    <option value="PARTIALLY_RECEIVED" {{ request('workflow_status') === 'PARTIALLY_RECEIVED' ? 'selected' : '' }}>Partially Received</option>
                    <option value="FULLY_RECEIVED" {{ request('workflow_status') === 'FULLY_RECEIVED' ? 'selected' : '' }}>Fully Received</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Filter</button>
        </form>
        <p class="mb-4 text-xs text-gray-600">
            Fully Received = count received equals count issued and issued weight matches total of (weight received + wastage).
        </p>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">MC Id</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Craftman</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Product</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Count issued</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Count received</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Weight issued</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Weight received</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Chargable Amount</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Paid Amount</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Created by</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $r)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 align-top">
                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $r->mc_id }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $r->craftman_display_name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $r->productType->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-right">{{ $r->count_issued }}</td>
                        <td class="px-4 py-2 text-sm text-right">{{ $r->total_count_received }}</td>
                        <td class="px-4 py-2 text-sm text-right">{{ number_format((float) $r->silver_gross_weight, 3) }}</td>
                        <td class="px-4 py-2 text-sm text-right">{{ number_format((float) $r->computed_weight_received, 3) }}</td>
                        <td class="px-4 py-2 text-sm text-right">{{ number_format($r->amount, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right">{{ number_format((float) ($r->paid_total ?? 0), 2) }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if($r->workflow_status === 'FULLY_RECEIVED')
                                <span class="px-2 py-0.5 rounded bg-green-100 text-green-800">Fully Received</span>
                            @elseif($r->workflow_status === 'PARTIALLY_RECEIVED')
                                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800">Partially Received</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-800">Issued</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $r->created_at->format('d-m-Y') }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $r->creator?->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if(Auth::check() && Auth::user()->is_admin)
                                <a href="{{ route('making-mc.changelog', $r) }}" title="Changelog" class="inline-block align-middle mr-1">
                                    <svg class="w-5 h-5 inline-block text-purple-600 hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </a>
                            @endif
                            <a href="{{ route('making-mc.show', $r) }}" class="text-indigo-600 hover:underline">View</a>
                            @if($r->workflow_status !== 'FULLY_RECEIVED')
                                | <a href="{{ route('making-mc.returns.create', $r) }}" class="text-green-600 hover:underline">Add return</a>
                            @endif
                            | <a href="{{ route('making-mc.edit', $r) }}" class="text-gray-600 hover:underline">Edit</a>
                            @if(Auth::check() && Auth::user()->is_admin)
                                | <a href="{{ route('making-mc.delete', $r) }}" class="text-red-600 hover:underline">Delete</a>
                            @endif
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 bg-slate-50/90">
                        <td colspan="13" class="px-4 py-2">
                            <details class="return-details-block border-l-4 border-indigo-400 pl-4 pr-1">
                                <summary class="flex cursor-pointer list-none items-center gap-2 py-1 text-left [&::-webkit-details-marker]:hidden">
                                    <svg class="return-details-chevron h-4 w-4 shrink-0 text-indigo-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-indigo-800">Return details</span>
                                    @if($r->returns->isNotEmpty())
                                        <span class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800">{{ $r->returns->count() }} {{ $r->returns->count() === 1 ? 'entry' : 'entries' }}</span>
                                    @else
                                        <span class="text-xs font-normal normal-case text-gray-500">— none yet</span>
                                    @endif
                                    <span class="return-details-hint-closed text-xs text-gray-400">· click to expand</span>
                                    <span class="return-details-hint-open text-xs text-gray-400">· click to collapse</span>
                                </summary>
                                <div class="mt-3 border-t border-gray-200 pt-3">
                                    <div class="mb-2 flex justify-end">
                                        <a href="{{ route('making-mc.show', $r) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline">Open full order →</a>
                                    </div>
                                    @if($r->returns->isEmpty())
                                        <p class="text-xs text-gray-500 italic">No returns recorded yet.</p>
                                    @else
                                        <div class="overflow-x-auto rounded border border-gray-200 bg-white shadow-sm">
                                            <table class="min-w-full divide-y divide-gray-100 text-xs">
                                                <thead class="bg-gray-50 text-[11px] uppercase text-gray-500">
                                                    <tr>
                                                        <th class="px-3 py-1.5 text-left font-medium">Date</th>
                                                        <th class="px-3 py-1.5 text-right font-medium">Count</th>
                                                        <th class="px-3 py-1.5 text-right font-medium">Weight (g)</th>
                                                        <th class="px-3 py-1.5 text-right font-medium">Wastage (g)</th>
                                                        <th class="px-3 py-1.5 text-right font-medium">Total (g)</th>
                                                        <th class="px-3 py-1.5 text-right font-medium">Paid</th>
                                                        <th class="px-3 py-1.5 text-left font-medium min-w-[8rem]">Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-50 text-gray-800">
                                                    @foreach($r->returns as $ret)
                                                    <tr class="hover:bg-gray-50/80">
                                                        <td class="px-3 py-1.5 whitespace-nowrap">{{ $ret->received_at->format('d-m-Y') }}</td>
                                                        <td class="px-3 py-1.5 text-right tabular-nums">{{ $ret->count_received }}</td>
                                                        <td class="px-3 py-1.5 text-right tabular-nums">{{ number_format((float) $ret->weight_received, 3) }}</td>
                                                        <td class="px-3 py-1.5 text-right tabular-nums">{{ number_format((float) ($ret->wastage_grams ?? 0), 3) }}</td>
                                                        <td class="px-3 py-1.5 text-right tabular-nums font-medium text-gray-900">{{ number_format((float) $ret->weight_received + (float) ($ret->wastage_grams ?? 0), 3) }}</td>
                                                        <td class="px-3 py-1.5 text-right tabular-nums">{{ number_format((float) ($ret->amount_paid ?? 0), 2) }}</td>
                                                        <td class="px-3 py-1.5 text-gray-600 max-w-xs">
                                                            <span class="line-clamp-2" @if($ret->remarks) title="{{ e($ret->remarks) }}" @endif>{{ $ret->remarks ? $ret->remarks : '—' }}</span>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </details>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="13" class="px-4 py-8 text-center text-gray-500">No labour receipts yet. Create one to get started.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr>
                        <td class="px-4 py-2 text-sm font-semibold text-gray-900" colspan="3">Grand Total</td>
                        <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900">{{ $grandTotals['count_issued'] ?? 0 }}</td>
                        <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900">{{ $grandTotals['count_received'] ?? 0 }}</td>
                        <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900">{{ number_format((float) ($grandTotals['weight_issued'] ?? 0), 3) }}</td>
                        <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900">{{ number_format((float) ($grandTotals['weight_received'] ?? 0), 3) }}</td>
                        <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900">{{ number_format((float) ($grandTotals['chargable_amount'] ?? 0), 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right font-semibold text-gray-900">{{ number_format((float) ($grandTotals['paid_amount'] ?? 0), 2) }}</td>
                        <td class="px-4 py-2" colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if($receipts->hasPages())
            <div class="mt-4">{{ $receipts->links() }}</div>
        @endif
    </div>
</div>
@endsection
