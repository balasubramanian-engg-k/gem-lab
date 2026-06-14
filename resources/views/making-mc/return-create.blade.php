@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Add return</h1>
        <div class="space-y-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Order ID</label>
                <input type="text" value="{{ $receipt->mc_id }}" readonly class="w-full border border-gray-200 rounded-lg px-4 py-2 bg-gray-50 text-gray-700 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product name</label>
                <input type="text" value="{{ $receipt->productType->name ?? '-' }}" readonly class="w-full border border-gray-200 rounded-lg px-4 py-2 bg-gray-50 text-gray-700 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Craftsman name</label>
                <input type="text" value="{{ $receipt->craftman_display_name }}" readonly class="w-full border border-gray-200 rounded-lg px-4 py-2 bg-gray-50 text-gray-700 cursor-not-allowed">
            </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">Issued: {{ $receipt->count_issued }} pcs</p>
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('making-mc.returns.store', $receipt) }}" class="space-y-4">
            @csrf
            <div>
                <label for="count_received" class="block text-sm font-medium text-gray-700 mb-1">Count received (this batch) <span class="text-red-500">*</span></label>
                <input type="number" name="count_received" id="count_received" min="0" value="{{ old('count_received') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="weight_received" class="block text-sm font-medium text-gray-700 mb-1">Weight received (g)</label>
                <input type="number" name="weight_received" id="weight_received" step="0.001" min="0" value="{{ old('weight_received') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="wastage_grams" class="block text-sm font-medium text-gray-700 mb-1">Wastage (g) <span class="text-red-500">*</span></label>
                <input type="number" name="wastage_grams" id="wastage_grams" step="0.001" min="0" value="{{ old('wastage_grams') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Paid <span class="text-red-500">*</span></label>
                <input type="number" name="amount_paid" id="amount_paid" step="0.01" min="0" value="{{ old('amount_paid') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="received_at" class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                <input type="date" name="received_at" id="received_at" value="{{ old('received_at', date('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" id="remarks" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('remarks') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Save return</button>
                <a href="{{ route('making-mc.show', $receipt) }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
