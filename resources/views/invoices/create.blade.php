@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Create Invoice</h1>
            <a href="{{ route('invoices.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Back to Invoices</a>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
            @csrf
            
            <!-- Customer and Cost Fields -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-4">Invoice Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                               class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location <span class="text-red-500">*</span></label>
                        <input type="text" name="location" value="{{ old('location') }}" required
                               class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Count <span class="text-red-500">*</span></label>
                        <input type="number" name="total_count" min="0" value="{{ old('total_count') }}" required
                               class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Actual Silver Weight <span class="text-red-500">*</span></label>
                        <input type="number" name="actual_silver_weight" step="0.01" min="0" value="{{ old('actual_silver_weight', '0.00') }}" required
                               class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Wastage+Making+Certification Cost <span class="text-red-500">*</span></label>
                        <input type="number" name="wastage_making_certification_cost" step="0.01" min="0" value="{{ old('wastage_making_certification_cost', '0.00') }}" required
                               class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Type <span class="text-red-500">*</span></label>
                        <select name="product_type_id" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select Product Type</option>
                            @foreach($productTypes as $productType)
                                <option value="{{ $productType->id }}" {{ old('product_type_id') == $productType->id ? 'selected' : '' }}>{{ $productType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select Status</option>
                            @foreach(\App\Models\Invoice::STATUSES as $s)
                                <option value="{{ $s }}" {{ old('status', 'NEW') == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assignee Name <span class="text-red-500">*</span></label>
                        <input type="text" name="assignee_name" value="{{ old('assignee_name') }}" required
                               class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks <span class="text-red-500">*</span></label>
                        <textarea name="remarks" rows="3" required
                                  class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">{{ old('remarks') }}</textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}"
                               class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Silver Rate</label>
                        <input type="number" name="silver_rate" step="0.01" min="0" value="{{ old('silver_rate', '0.00') }}"
                               class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g. per gram">
                    </div>
                </div>
            </div>
            
            <div id="productsContainer">
                <!-- Product rows will be added here -->
            </div>

            <div class="mt-4">
                <button type="button" id="addProductBtn" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    + Add Product
                </button>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('invoices.index') }}"
                   class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Cancel</a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Invoice</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productsContainer = document.getElementById('productsContainer');
    const addProductBtn = document.getElementById('addProductBtn');
    let productCount = 0;

    const stones = @json($stones);

    // Add first product row
    addProductRow();

    addProductBtn.addEventListener('click', function() {
        addProductRow();
    });

    function addProductRow() {
        productCount++;
        const row = document.createElement('div');
        row.className = 'product-row mb-4 p-4 border border-gray-200 rounded-lg';
        row.dataset.index = productCount;

        row.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <h4 class="font-semibold text-gray-700">Product ${productCount}</h4>
                <button type="button" class="remove-product text-red-600 hover:text-red-800" onclick="removeProductRow(this)">
                    Remove
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tag name <span class="text-red-500">*</span></label>
                    <select name="products[${productCount}][stone]" required
                            class="stone-select w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500"
                            onchange="calculateCosts(this)">
                        <option value="">Select Stone</option>
                        ${stones.map((stone, index) => `
                            <option value="${stone.id}" data-rate="${stone.rate_per_piece}" ${index === 0 ? 'selected' : ''}>
                                ${stone.stone_name}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ring Size</label>
                    <input type="text" name="products[${productCount}][ring_size]"
                           class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stone Weight <span class="text-red-500">*</span></label>
                    <input type="number" name="products[${productCount}][stone_weight]" step="0.01" min="0" value="0.00" required
                           class="stone-weight w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500"
                           oninput="calculateCosts(this)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gross Weight <span class="text-red-500">*</span></label>
                    <input type="number" name="products[${productCount}][gross_weight]" step="0.01" min="0" value="0.00" required
                           class="gross-weight w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500"
                           oninput="calculateCosts(this)">
                </div>
            </div>
        `;

        productsContainer.appendChild(row);
    }

    window.removeProductRow = function(btn) {
        const row = btn.closest('.product-row');
        row.remove();
    };

    window.calculateCosts = function(element) {
        // Calculation removed - no fields to update
    };
});
</script>
@endsection
