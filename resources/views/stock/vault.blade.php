@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Stock in Vault</h1>

        <form method="POST" action="{{ route('stock.storeVault') }}" class="space-y-4">
            @csrf
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (grams) <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="amount" step="0.001" min="0" value="{{ old('amount', $vaultStock) }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Save</button>
                <a href="{{ route('stock.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
