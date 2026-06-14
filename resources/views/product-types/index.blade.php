@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Product Types Listing</h1>
            <a href="{{ route('product-types.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                Add Product Type
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Search Bar -->
        <form method="GET" action="{{ route('product-types.index') }}" class="mb-4">
            <div class="flex items-center gap-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search product types..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('product-types.index') }}" 
                       class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">ID</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Product Type Name</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Created At</th>
                        <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productTypes as $productType)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $productType->id }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $productType->name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $productType->created_at ? \Carbon\Carbon::parse($productType->created_at)->format('Y-m-d') : '-' }}</td>
                        <td class="px-4 py-2 text-center space-x-2">
                            <!-- Edit -->
                            <a href="{{ route('product-types.edit', $productType->id) }}" title="Edit" class="inline-block">
                                <img src="{{ asset('icons/edit.svg') }}" class="w-5 h-5 inline-block hover:scale-110 transition">
                            </a>

                            <!-- Delete -->
                            <form action="{{ route('product-types.destroy', $productType->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this product type?');">
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
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">No product types found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $productTypes->links() }}
        </div>
    </div>
</div>
@endsection
