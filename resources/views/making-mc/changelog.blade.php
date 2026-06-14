@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Labour receipt changelog</h1>
                <p class="text-gray-600 mt-1">Order {{ $receipt->mc_id }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('making-mc.show', $receipt) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Back to order
                </a>
                <a href="{{ route('making-mc.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Making MC list
                </a>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($changelogs as $changelog)
                <div class="border-l-4 pl-4 pb-4 relative
                    @if($changelog->action == 'created') border-green-500
                    @elseif($changelog->action == 'status_changed') border-yellow-500
                    @elseif($changelog->action == 'return_added') border-emerald-500
                    @elseif($changelog->action == 'return_deleted') border-orange-500
                    @else border-blue-500
                    @endif">
                    <div class="absolute -left-2 top-0 w-4 h-4 rounded-full border-2 border-white shadow
                        @if($changelog->action == 'created') bg-green-500
                        @elseif($changelog->action == 'status_changed') bg-yellow-500
                        @elseif($changelog->action == 'return_added') bg-emerald-500
                        @elseif($changelog->action == 'return_deleted') bg-orange-500
                        @else bg-blue-500
                        @endif"></div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-3 flex-wrap">
                                @if($changelog->action == 'created')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">Created</span>
                                @elseif($changelog->action == 'status_changed')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Status changed</span>
                                @elseif($changelog->action == 'return_added')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Return added</span>
                                @elseif($changelog->action == 'return_deleted')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-orange-100 text-orange-800">Return deleted</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">Updated</span>
                                @endif
                                @if($changelog->field_label)
                                    <span class="text-sm font-medium text-gray-700">{{ $changelog->field_label }}</span>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-700">{{ $changelog->created_at->format('d-m-Y H:i:s') }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($changelog->user)
                                        {{ $changelog->user->name }}
                                    @else
                                        System
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($changelog->description)
                            <p class="text-sm text-gray-600 mb-2">{{ $changelog->description }}</p>
                        @endif

                        @if($changelog->old_value !== null && $changelog->new_value !== null && $changelog->action !== 'return_added' && $changelog->action !== 'return_deleted')
                            <div class="mt-3 p-3 bg-white rounded border border-gray-200">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-xs font-medium text-gray-500 mb-1">Old value</div>
                                        <div class="text-sm text-red-600 line-through">{{ $changelog->old_value }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-gray-500 mb-1">New value</div>
                                        <div class="text-sm text-green-600 font-medium">{{ $changelog->new_value }}</div>
                                    </div>
                                </div>
                            </div>
                        @elseif($changelog->action == 'created')
                            <div class="mt-3 p-3 bg-white rounded border border-gray-200 text-sm text-gray-700">
                                Labour receipt was created.
                            </div>
                        @endif

                        @if($changelog->ip_address)
                            <div class="mt-2 text-xs text-gray-400">IP: {{ $changelog->ip_address }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">No changelog entries yet.</p>
                </div>
            @endforelse
        </div>

        @if($changelogs->hasPages())
            <div class="mt-6">{{ $changelogs->links() }}</div>
        @endif
    </div>
</div>
@endsection
