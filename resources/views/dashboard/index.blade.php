@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-6">

    <!-- Welcome Card -->
    <div class="bg-white shadow rounded-lg p-6 col-span-3">
        <h2 class="text-lg font-semibold">Welcome, {{ Auth::user()->name }} 👋</h2>
        <p class="text-gray-600 mt-2">This is your GHC-Gemstone Hallmark Centre dashboard.</p>
    </div>


</div>


@endsection
