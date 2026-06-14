@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Craftman Ledger Report</h1>
        <p class="text-gray-600 mb-6">Enter the craftman name to generate the ledger report (PDF or view on screen).</p>
        <form method="GET" action="{{ route('report.craftman-ledger.view') }}" class="space-y-4">
            <div>
                <label for="craftman_name" class="block text-sm font-medium text-gray-700 mb-1">Craftman name <span class="text-red-500">*</span></label>
                <input type="text" name="craftman_name" id="craftman_name" value="{{ request('craftman_name') }}" required maxlength="255" placeholder="Enter craftman name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date from</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date to</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">View report</button>
            </div>
        </form>
        <form method="GET" action="{{ route('report.craftman-ledger.pdf') }}" class="mt-4 inline-block" id="pdfForm">
            <input type="hidden" name="craftman_name" id="pdf_craftman_name" value="{{ request('craftman_name') }}">
            <input type="hidden" name="date_from" id="pdf_date_from" value="{{ request('date_from') }}">
            <input type="hidden" name="date_to" id="pdf_date_to" value="{{ request('date_to') }}">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">Download PDF</button>
        </form>
        <p class="mt-4 text-sm text-gray-500">Use "View report" for mobile-friendly view. Enter craftman name and optional dates, then "Download PDF" for the same data as PDF.</p>
    </div>
</div>
<script>
document.querySelector('form[action="{{ route('report.craftman-ledger.view') }}"]').addEventListener('submit', function() {
    document.getElementById('pdf_craftman_name').value = document.getElementById('craftman_name').value;
    document.getElementById('pdf_date_from').value = document.getElementById('date_from').value;
    document.getElementById('pdf_date_to').value = document.getElementById('date_to').value;
});
document.getElementById('pdfForm').addEventListener('submit', function() {
    document.getElementById('pdf_craftman_name').value = document.getElementById('craftman_name').value;
    document.getElementById('pdf_date_from').value = document.getElementById('date_from').value;
    document.getElementById('pdf_date_to').value = document.getElementById('date_to').value;
});
</script>
@endsection
