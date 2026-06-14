@extends('layouts.app')

@section('content')
<div class="max-w-8xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Invoices</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('invoices.export', request()->query()) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export to Excel
                </a>
                <a href="{{ route('invoices.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                    Create Invoice
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Notification Container -->
        <div id="notification" class="fixed top-4 right-4 z-50 hidden">
            <div class="bg-white border-l-4 rounded-lg shadow-lg p-4 min-w-80">
                <div class="flex items-center">
                    <div id="notificationIcon" class="flex-shrink-0 mr-3"></div>
                    <div class="flex-1">
                        <p id="notificationMessage" class="text-sm font-medium text-gray-800"></p>
                    </div>
                    <button onclick="closeNotification()" class="ml-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="mb-6">
            <form method="GET" action="{{ route('invoices.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by Invoice ID, Customer Name, Location, Assignee, Product Type, or Status..."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Search
                </button>
                <a href="{{ route('invoices.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
                    Clear
                </a>
            </form>
        </div>

        <!-- Pagination -->
        @if($invoices->hasPages())
            <div class="mb-4">
                {{ $invoices->links() }}
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        @php
                            $currentSortBy = request('sort_by', 'created_at');
                            $currentSortOrder = request('sort_order', 'desc');
                            
                            function getSortUrl($column, $currentSortBy, $currentSortOrder) {
                                $newSortOrder = ($currentSortBy == $column && $currentSortOrder == 'asc') ? 'desc' : 'asc';
                                return request()->fullUrlWithQuery(['sort_by' => $column, 'sort_order' => $newSortOrder]);
                            }
                            
                            function getSortIcon($column, $currentSortBy, $currentSortOrder) {
                                if ($currentSortBy != $column) {
                                    return '<svg class="w-4 h-4 inline-block ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>';
                                }
                                if ($currentSortOrder == 'asc') {
                                    return '<svg class="w-4 h-4 inline-block ml-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>';
                                } else {
                                    return '<svg class="w-4 h-4 inline-block ml-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                                }
                            }
                        @endphp
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                            <a href="{{ getSortUrl('id', $currentSortBy, $currentSortOrder) }}" class="flex items-center hover:text-blue-600">
                                Invoice Id
                                {!! getSortIcon('id', $currentSortBy, $currentSortOrder) !!}
                            </a>
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                            <a href="{{ getSortUrl('customer_name', $currentSortBy, $currentSortOrder) }}" class="flex items-center hover:text-blue-600">
                                Customer Name
                                {!! getSortIcon('customer_name', $currentSortBy, $currentSortOrder) !!}
                            </a>
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                            <a href="{{ getSortUrl('location', $currentSortBy, $currentSortOrder) }}" class="flex items-center hover:text-blue-600">
                                Location
                                {!! getSortIcon('location', $currentSortBy, $currentSortOrder) !!}
                            </a>
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                            <a href="{{ getSortUrl('product_type', $currentSortBy, $currentSortOrder) }}" class="flex items-center hover:text-blue-600">
                                Product Type
                                {!! getSortIcon('product_type', $currentSortBy, $currentSortOrder) !!}
                            </a>
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                            <a href="{{ getSortUrl('total_count', $currentSortBy, $currentSortOrder) }}" class="flex items-center hover:text-blue-600">
                                Total Count
                                {!! getSortIcon('total_count', $currentSortBy, $currentSortOrder) !!}
                            </a>
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                            <a href="{{ getSortUrl('assignee_name', $currentSortBy, $currentSortOrder) }}" class="flex items-center hover:text-blue-600">
                                Assignee
                                {!! getSortIcon('assignee_name', $currentSortBy, $currentSortOrder) !!}
                            </a>
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                            <a href="{{ getSortUrl('product_count', $currentSortBy, $currentSortOrder) }}" class="flex items-center hover:text-blue-600">
                                Product Count
                                {!! getSortIcon('product_count', $currentSortBy, $currentSortOrder) !!}
                            </a>
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                            <a href="{{ getSortUrl('created_at', $currentSortBy, $currentSortOrder) }}" class="flex items-center hover:text-blue-600">
                                Created Date
                                {!! getSortIcon('created_at', $currentSortBy, $currentSortOrder) !!}
                            </a>
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                            <a href="{{ getSortUrl('status', $currentSortBy, $currentSortOrder) }}" class="flex items-center hover:text-blue-600">
                                Status
                                {!! getSortIcon('status', $currentSortBy, $currentSortOrder) !!}
                            </a>
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                            <a href="{{ getSortUrl('delivered_date', $currentSortBy, $currentSortOrder) }}" class="flex items-center hover:text-blue-600">
                                Delivered On
                                {!! getSortIcon('delivered_date', $currentSortBy, $currentSortOrder) !!}
                            </a>
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                            <a href="{{ getSortUrl('due_date', $currentSortBy, $currentSortOrder) }}" class="flex items-center hover:text-blue-600">
                                Due Date
                                {!! getSortIcon('due_date', $currentSortBy, $currentSortOrder) !!}
                            </a>
                        </th>
                        <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-700">AD{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $invoice->customer_name ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $invoice->location ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $invoice->productType->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $invoice->total_count ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $invoice->assignee_name ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $invoice->invoiceDetails->count() }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') : '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded
                                @if($invoice->status == 'NEW') bg-blue-100 text-blue-800
                                @elseif($invoice->status == 'OPENED') bg-yellow-100 text-yellow-800
                                @elseif($invoice->status == 'SETTING') bg-purple-100 text-purple-800
                                @elseif($invoice->status == 'PACKAGING') bg-indigo-100 text-indigo-800
                                @elseif($invoice->status == 'COMPLETED') bg-green-100 text-green-800
                                @elseif($invoice->status == 'DELIVERED') bg-emerald-100 text-emerald-800
                                @elseif($invoice->status == 'CANCELLED') bg-red-100 text-red-800
                                @elseif($invoice->status == 'PAID') bg-gray-100 text-gray-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $invoice->status ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            {{ $invoice->delivered_date ? \Carbon\Carbon::parse($invoice->delivered_date)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="px-4 py-2 text-center space-x-2">
                            <button type="button" onclick="toggleSilverCost({{ $invoice->id }})" title="Toggle Silver Cost (weight &amp; assignee on invoice)" class="inline-block toggle-silver-cost-{{ $invoice->id }}">
                                <svg class="w-5 h-5 inline-block hover:scale-110 transition {{ $invoice->toggle_silver_cost ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                            <button type="button" onclick="toggleSilverRate({{ $invoice->id }})" title="Toggle Silver Rate on invoice / PDF" class="inline-flex items-center justify-center w-8 h-8 rounded border text-sm font-bold leading-none hover:scale-105 transition toggle-silver-rate-{{ $invoice->id }} {{ $invoice->toggle_silver_rate ? 'border-amber-400 bg-amber-50 text-amber-700' : 'border-gray-200 bg-white text-gray-400' }}">
                                ₹
                            </button>
                                <a href="{{ route('invoices.changelog', $invoice->id) }}" title="View Changelog" class="inline-block">
                                    <svg class="w-5 h-5 inline-block hover:scale-110 transition text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                            <button onclick="viewInvoice({{ $invoice->id }})" title="View" class="inline-block">
                                <img src="{{ asset('icons/view.svg') }}" class="w-5 h-5 inline-block hover:scale-110 transition">
                            </button>
                            <a href="{{ route('invoices.downloadPdf', $invoice->id) }}" title="Download PDF" class="inline-block">
                                <svg class="w-5 h-5 inline-block hover:scale-110 transition text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('invoices.downloadXls', $invoice->id) }}" title="Download Excel (delivery note)" class="inline-block">
                                <svg class="w-5 h-5 inline-block hover:scale-110 transition text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18M8 5v14M13 5v14M18 5v14"></path>
                                </svg>
                            </a>
                            <a href="{{ route('invoices.edit', $invoice->id) }}" title="Edit" class="inline-block">
                                <img src="{{ asset('icons/edit.svg') }}" class="w-5 h-5 inline-block hover:scale-110 transition">
                            </a>
                            <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete" class="inline-block">
                                    <img src="{{ asset('icons/delete.svg') }}" class="w-5 h-5 inline-block hover:scale-110 transition">
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-4 py-6 text-center text-gray-500">No invoices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div id="invoiceModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden" style="overflow-y: auto;">
    <div class="min-h-screen py-8 px-4 flex items-start justify-center">
        <div class="relative bg-white rounded-lg shadow-xl max-w-5xl w-full my-8">
            <!-- Close Button -->
            <button onclick="closeInvoiceModal()" 
                    class="sticky top-4 float-right z-10 bg-white hover:bg-gray-100 text-gray-600 hover:text-gray-800 rounded-full p-2 shadow-md transition-colors duration-200 mr-4 mt-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <!-- Invoice Content -->
            <div id="invoiceContent" class="p-8">
                <!-- Loading state -->
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
    
    // Check if elements exist
    if (!modal || !content) {
        console.error('Modal elements not found');
        return;
    }
    
    // Show modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Show loading state
    content.innerHTML = '<div id="invoiceLoading" class="text-center py-12"><div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div><p class="mt-4 text-gray-600">Loading invoice...</p></div>';
    
    // Fetch invoice
    fetch(`{{ url('/gem-admin/invoices') }}/${invoiceId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            content.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<div class="text-center py-12 text-red-600">Error loading invoice. Please try again.</div>';
        });
}

function closeInvoiceModal() {
    const modal = document.getElementById('invoiceModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notificationMessage');
    const notificationIcon = document.getElementById('notificationIcon');
    
    notificationMessage.textContent = message;
    
    // Set icon and border color based on type
    if (type === 'success') {
        notification.classList.remove('border-red-500', 'border-yellow-500', 'border-blue-500');
        notification.classList.add('border-green-500');
        notificationIcon.innerHTML = `
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        `;
    } else if (type === 'error') {
        notification.classList.remove('border-green-500', 'border-yellow-500', 'border-blue-500');
        notification.classList.add('border-red-500');
        notificationIcon.innerHTML = `
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        `;
    } else if (type === 'info') {
        notification.classList.remove('border-green-500', 'border-red-500', 'border-yellow-500');
        notification.classList.add('border-blue-500');
        notificationIcon.innerHTML = `
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        `;
    } else {
        notification.classList.remove('border-green-500', 'border-red-500', 'border-blue-500');
        notification.classList.add('border-yellow-500');
        notificationIcon.innerHTML = `
            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        `;
    }
    
    notification.classList.remove('hidden');
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        closeNotification();
    }, 3000);
}

function closeNotification() {
    const notification = document.getElementById('notification');
    notification.classList.add('hidden');
}

function toggleSilverRate(invoiceId) {
    showNotification('Updating Silver Rate visibility...', 'info');

    fetch(`{{ url('/gem-admin/invoices') }}/${invoiceId}/toggle-silver-rate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.querySelector(`.toggle-silver-rate-${invoiceId}`);
            if (btn) {
                btn.classList.remove('border-amber-400', 'bg-amber-50', 'text-amber-700', 'border-gray-200', 'bg-white', 'text-gray-400');
                if (data.toggle_silver_rate) {
                    btn.classList.add('border-amber-400', 'bg-amber-50', 'text-amber-700');
                    showNotification('Silver Rate will show on invoice and PDF.', 'success');
                } else {
                    btn.classList.add('border-gray-200', 'bg-white', 'text-gray-400');
                    showNotification('Silver Rate hidden on invoice and PDF.', 'success');
                }
            }
        } else {
            showNotification('Failed to update Silver Rate toggle.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating Silver Rate toggle.', 'error');
    });
}

function toggleSilverCost(invoiceId) {
    // Show loading notification
    showNotification('Updating silver cost toggle...', 'info');
    
    fetch(`{{ url('/gem-admin/invoices') }}/${invoiceId}/toggle-silver-cost`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const toggleButton = document.querySelector(`.toggle-silver-cost-${invoiceId}`);
            const svg = toggleButton.querySelector('svg');
            if (data.toggle_silver_cost) {
                svg.classList.remove('text-gray-400');
                svg.classList.add('text-green-600');
                showNotification('Silver cost toggle enabled successfully!', 'success');
            } else {
                svg.classList.remove('text-green-600');
                svg.classList.add('text-gray-400');
                showNotification('Silver cost toggle disabled successfully!', 'success');
            }
        } else {
            showNotification('Failed to update toggle. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error toggling silver cost. Please try again.', 'error');
    });
}
</script>
@endsection
