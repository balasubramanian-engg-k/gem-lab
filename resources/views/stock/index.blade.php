@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Silver Stock</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('stock.vault') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                    Stock in Vault
                </a>
                <a href="{{ route('stock.add') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
                    Add stock
                </a>
                <a href="{{ route('stock.sell') }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg shadow hover:bg-amber-700 transition">
                    Sell silver
                </a>
                <a href="{{ route('stock.history') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg shadow hover:bg-gray-700 transition">
                    History
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Vault Stock</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($vaultStock, 3) }} <span class="text-base font-normal text-gray-600">g</span></p>
                <p class="text-xs text-gray-500 mt-1">Silver in vault (set via Stock in Vault)</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-6 bg-blue-50">
                <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Used stock</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($usedStock, 3) }} <span class="text-base font-normal text-gray-600">g</span></p>
                <p class="text-xs text-gray-500 mt-1">Consumed by invoices (reduced when you add stock)</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-6 {{ $remainingStock >= 0 ? 'bg-emerald-50' : 'bg-red-50' }}">
                <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Remaining stock</p>
                <p class="text-2xl font-bold {{ $remainingStock >= 0 ? 'text-gray-900' : 'text-red-700' }} mt-1">{{ number_format($remainingStock, 3) }} <span class="text-base font-normal text-gray-600">g</span></p>
                <p class="text-xs text-gray-500 mt-1">(Add − Sell) − total invoice usage (no add-stock offset)</p>
            </div>
        </div>

        <!-- Chart: Used vs Remaining -->
        <div class="mt-8 border border-gray-200 rounded-lg p-6 bg-white">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Used vs Remaining (grams)</h2>
            <div class="max-w-md mx-auto" style="height: 280px;">
                <canvas id="stockChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const used = {{ json_encode(round($usedStock, 2)) }};
    const remaining = {{ json_encode(round($remainingStock, 2)) }};
    const ctx = document.getElementById('stockChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Used stock (g)', 'Remaining stock (g)'],
            datasets: [{
                label: 'Grams',
                data: [used, remaining],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    remaining >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    'rgb(59, 130, 246)',
                    remaining >= 0 ? 'rgb(16, 185, 129)' : 'rgb(239, 68, 68)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' g';
                        }
                    }
                }
            },
            scales: {
                x: {
                    min: remaining < 0 ? Math.min(0, remaining * 1.2) : 0,
                    title: { display: true, text: 'Grams' }
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
