@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Delete order</h1>
        <p class="text-gray-600 mb-6">This action cannot be undone. The following order will be permanently removed.</p>

        <dl class="grid grid-cols-1 gap-3 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div><dt class="text-sm text-gray-500">Order ID</dt><dd class="font-medium">{{ $receipt->mc_id }}</dd></div>
            <div><dt class="text-sm text-gray-500">Craftman</dt><dd class="font-medium">{{ $receipt->craftman_display_name }}</dd></div>
            <div><dt class="text-sm text-gray-500">Product</dt><dd class="font-medium">{{ $receipt->productType->name ?? '-' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Count issued</dt><dd class="font-medium">{{ $receipt->count_issued }}</dd></div>
            <div><dt class="text-sm text-gray-500">To Pay</dt><dd class="font-medium">{{ number_format($receipt->amount, 2) }}</dd></div>
        </dl>

        <form method="POST" action="{{ route('making-mc.destroy', $receipt) }}" class="flex flex-wrap gap-3">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">Delete order</button>
            <a href="{{ route('making-mc.show', $receipt) }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Cancel</a>
            <a href="{{ route('making-mc.index') }}" class="px-6 py-2 text-gray-600 hover:underline">Back to list</a>
        </form>
    </div>
</div>
@endsection
