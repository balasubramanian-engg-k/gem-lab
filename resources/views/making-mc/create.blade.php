@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Create Labour receipt</h1>
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('making-mc.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="craftman_name" class="block text-sm font-medium text-gray-700 mb-1">Craftman Name <span class="text-red-500">*</span></label>
                <input type="text" name="craftman_name" id="craftman_name" value="{{ old('craftman_name') }}" required maxlength="255" placeholder="Enter craftman name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="product_type_id" class="block text-sm font-medium text-gray-700 mb-1">Product name <span class="text-red-500">*</span></label>
                <select name="product_type_id" id="product_type_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select product</option>
                    @foreach($productTypes as $pt)
                        <option value="{{ $pt->id }}" {{ old('product_type_id') == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="count_issued" class="block text-sm font-medium text-gray-700 mb-1">Count issued <span class="text-red-500">*</span></label>
                <input type="number" name="count_issued" id="count_issued" min="1" value="{{ old('count_issued') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="silver_gross_weight" class="block text-sm font-medium text-gray-700 mb-1">Silver gross weight (g) <span class="text-red-500">*</span></label>
                <input type="number" name="silver_gross_weight" id="silver_gross_weight" step="0.001" min="0" value="{{ old('silver_gross_weight') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">To Pay <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="amount" step="0.01" min="0" value="{{ old('amount') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" id="remarks" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('remarks') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Create</button>
                <a href="{{ route('making-mc.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
