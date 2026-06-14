@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-6">

    <!-- Welcome Card -->
    <div class="bg-white shadow rounded-lg p-6 col-span-3">
        <h2 class="text-lg font-semibold">Welcome, {{ Auth::user()->name }} 👋</h2>
        <p class="text-gray-600 mt-2">This is your gem lab admin dashboard.</p>
    </div>

    <!-- Stats Cards -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-sm font-medium text-gray-500">Total Gems</h3>
        <p class="text-3xl font-bold mt-2 text-indigo-600">{{ $totalGems ?? 0 }}</p>
    </div>

   

</div>


@endsection
