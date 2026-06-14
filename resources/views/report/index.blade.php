@extends('layouts.app')

@section('content')
<div class="max-w-8xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Report</h1>

        <p class="text-gray-600 mb-6">Select filters and click Download xls to download the stone details report.</p>

        <form method="GET" action="{{ route('report.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-[200px]">
                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                <select name="customer_name" id="customer_name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="" {{ request('customer_name') === '' || !request()->has('customer_name') ? 'selected' : '' }}>All Customers</option>
                    @foreach($customerNames as $name)
                        <option value="{{ $name }}" {{ request('customer_name') == $name ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[200px]">
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <select name="location" id="location" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="" {{ request('location') === '' || !request()->has('location') ? 'selected' : '' }}>All Locations</option>
                    @foreach($locations as $loc)
                        @php
                            $val = ($loc === null || $loc === '') ? '__blank__' : $loc;
                            $label = ($loc === null || $loc === '') ? '[No location]' : $loc;
                        @endphp
                        <option value="{{ $val }}" {{ request('location') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[200px]">
                <label for="product_type_id" class="block text-sm font-medium text-gray-700 mb-1">Product Type</label>
                <select name="product_type_id" id="product_type_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="" {{ request('product_type_id') === '' || !request()->has('product_type_id') ? 'selected' : '' }}>All Product Types</option>
                    @foreach($productTypes as $pt)
                        <option value="{{ $pt->id }}" {{ request('product_type_id') == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                    Download xls
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
