@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ $craftman->name }}</h1>
            <a href="{{ route('craftmen.edit', $craftman) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">Edit</a>
        </div>
        <dl class="space-y-2">
            <div><dt class="text-sm text-gray-500">Phone</dt><dd class="font-medium">{{ $craftman->phone ?? '-' }}</dd></div>
        </dl>
        <p class="mt-4 text-sm text-gray-500">Used in Making Labour Receipts. <a href="{{ route('making-mc.index', ['craftman_id' => $craftman->id]) }}" class="text-indigo-600 hover:underline">View orders</a></p>
    </div>
</div>
@endsection
