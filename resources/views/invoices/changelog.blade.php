@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Invoice Changelog</h1>
                <p class="text-gray-600 mt-1">Invoice #AD{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('invoices.index') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Back to Invoices
                </a>
            </div>
        </div>

        <!-- Changelog Timeline -->
        <div class="space-y-4">
            @forelse($changelogs as $changelog)
                <div class="border-l-4 
                    @if($changelog->action == 'created') border-green-500
                    @elseif($changelog->action == 'status_changed') border-yellow-500
                    @elseif($changelog->action == 'deleted') border-red-500
                    @else border-blue-500
                    @endif pl-4 pb-4 relative">
                    
                    <!-- Timeline dot -->
                    <div class="absolute -left-2 top-0 w-4 h-4 rounded-full 
                        @if($changelog->action == 'created') bg-green-500
                        @elseif($changelog->action == 'status_changed') bg-yellow-500
                        @elseif($changelog->action == 'deleted') bg-red-500
                        @else bg-blue-500
                        @endif border-2 border-white shadow"></div>
                    
                    <!-- Content -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-3">
                                @if($changelog->action == 'created')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                        ✅ Created
                                    </span>
                                @elseif($changelog->action == 'status_changed')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        🔄 Status Changed
                                    </span>
                                @elseif($changelog->action == 'deleted')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                        🗑️ Deleted
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        ✏️ Updated
                                    </span>
                                @endif
                                
                                @if($changelog->field_label)
                                    <span class="text-sm font-medium text-gray-700">{{ $changelog->field_label }}</span>
                                @endif
                            </div>
                            
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-700">
                                    {{ $changelog->created_at->format('d-m-Y H:i:s') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if($changelog->user)
                                        👤 {{ $changelog->user->name }}
                                    @else
                                        👤 System
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        @if($changelog->description)
                            <p class="text-sm text-gray-600 mb-2">{{ $changelog->description }}</p>
                        @endif
                        
                        <!-- Value Changes -->
                        @if($changelog->old_value !== null && $changelog->new_value !== null)
                            <div class="mt-3 p-3 bg-white rounded border border-gray-200">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-xs font-medium text-gray-500 mb-1">Old Value</div>
                                        <div class="text-sm text-red-600 line-through">{{ $changelog->old_value }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-gray-500 mb-1">New Value</div>
                                        <div class="text-sm text-green-600 font-medium">{{ $changelog->new_value }}</div>
                                    </div>
                                </div>
                            </div>
                        @elseif($changelog->action == 'created')
                            <div class="mt-3 p-3 bg-white rounded border border-gray-200">
                                <div class="text-sm text-gray-700">
                                    Invoice was created with initial values.
                                </div>
                            </div>
                        @endif
                        
                        <!-- Additional Info -->
                        @if($changelog->ip_address)
                            <div class="mt-2 text-xs text-gray-400">
                                IP: {{ $changelog->ip_address }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">📋</div>
                    <p class="text-gray-500 text-lg">No changelog entries found for this invoice.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($changelogs->hasPages())
            <div class="mt-6">
                {{ $changelogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
