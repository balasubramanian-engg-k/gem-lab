@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Silver stock history</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('stock.history.export') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export to Excel
                </a>
                <a href="{{ route('stock.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">Back to stock</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">S.No</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Weight(gms)</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Remarks</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Invoice</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Customer</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Location</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">User</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $transactions->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $t->transaction_date->format('d-m-Y') }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if($t->type === 'add')
                                <span class="px-2 py-0.5 rounded bg-green-100 text-green-800">Add stock</span>
                            @elseif($t->type === 'sell')
                                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800">Sell</span>
                            @elseif($t->type === 'vault_update')
                                <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800">Vault update</span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800">Invoice usage</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-right font-medium">{{ number_format($t->amount, 3) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $t->remarks ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            @if($t->invoice_id)
                                <button type="button" onclick="viewInvoice({{ $t->invoice_id }})" class="text-indigo-600 hover:underline text-left">AD{{ str_pad($t->invoice_id, 6, '0', STR_PAD_LEFT) }}</button>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $t->invoice?->customer_name ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $t->invoice?->location ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $t->user?->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            @if($t->type === 'add')
                                <a href="{{ route('stock.transactions.edit', $t) }}" class="text-indigo-600 hover:underline">Edit</a>
                                <form action="{{ route('stock.transactions.destroy', $t) }}" method="POST" class="inline-block ml-1" onsubmit="return confirm('Delete this Add stock transaction? This will reverse the vault offset.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            @elseif($t->type === 'sell')
                                <a href="{{ route('stock.transactions.edit', $t) }}" class="text-indigo-600 hover:underline">Edit</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">No transactions yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Invoice Modal (same as invoice index) -->
<div id="invoiceModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden" style="overflow-y: auto;">
    <div class="min-h-screen py-8 px-4 flex items-start justify-center">
        <div class="relative bg-white rounded-lg shadow-xl max-w-5xl w-full my-8">
            <button onclick="closeInvoiceModal()" class="sticky top-4 float-right z-10 bg-white hover:bg-gray-100 text-gray-600 hover:text-gray-800 rounded-full p-2 shadow-md transition-colors duration-200 mr-4 mt-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div id="invoiceContent" class="p-8">
                <div id="invoiceLoading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
                    <p class="mt-4 text-gray-600">Loading invoice...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewInvoice(invoiceId) {
    const modal = document.getElementById('invoiceModal');
    const content = document.getElementById('invoiceContent');
    if (!modal || !content) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.innerHTML = '<div id="invoiceLoading" class="text-center py-12"><div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div><p class="mt-4 text-gray-600">Loading invoice...</p></div>';
    fetch('{{ url("/gem-admin/invoices") }}/' + invoiceId)
        .then(response => response.ok ? response.text() : Promise.reject(new Error('Not ok')))
        .then(html => { content.innerHTML = html; })
        .catch(() => { content.innerHTML = '<div class="text-center py-12 text-red-600">Error loading invoice. Please try again.</div>'; });
}
function closeInvoiceModal() {
    const modal = document.getElementById('invoiceModal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
}
</script>
@endsection
