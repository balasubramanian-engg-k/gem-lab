@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Craftmen</h1>
            <a href="{{ route('craftmen.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">Add Craftman</a>
        </div>
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        <form method="GET" action="{{ route('craftmen.index') }}" class="mb-4 flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or phone" class="flex-1 border border-gray-300 rounded-lg px-4 py-2">
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Search</button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Name</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Phone</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($craftmen as $c)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $c->name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $c->phone ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm">
                            <a href="{{ route('craftmen.edit', $c) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('craftmen.destroy', $c) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Delete this craftman?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">No craftmen yet. Add one to use in Labour Report.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($craftmen->hasPages())
            <div class="mt-4">{{ $craftmen->links() }}</div>
        @endif
    </div>
</div>
@endsection
