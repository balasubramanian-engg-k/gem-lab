@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Edit Stone</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('stones.update', $stone->id) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')
        
        <div>
            <label for="stone_name" class="block font-medium text-gray-700">Stone Name</label>
            <input required type="text" name="stone_name" id="stone_name"
                   value="{{ old('stone_name', $stone->stone_name) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="rate_per_piece" class="block font-medium text-gray-700">Rate Per Piece (₹)</label>
            <input required type="number" name="rate_per_piece" id="rate_per_piece" step="0.01" min="0"
                   value="{{ old('rate_per_piece', $stone->rate_per_piece) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('stones.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
        </div>
    </form>
</div>
@endsection
