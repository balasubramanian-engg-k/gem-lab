<?php

namespace App\Http\Controllers;

use App\Models\Stone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Stone::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('stone_name', 'LIKE', "%{$search}%")
                  ->orWhere('rate_per_piece', 'LIKE', "%{$search}%");
            });
        }

        $stones = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('stones.index', compact('stones', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('stones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'stone_name' => 'required|string|max:255',
            'rate_per_piece' => 'required|numeric|min:0',
        ]);

        Stone::create($request->all());

        return redirect()->route('stones.index')->with('success', 'Stone added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Stone $stone)
    {
        return view('stones.show', compact('stone'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stone $stone)
    {
        return view('stones.edit', compact('stone'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stone $stone)
    {
        $request->validate([
            'stone_name' => 'required|string|max:255',
            'rate_per_piece' => 'required|numeric|min:0',
        ]);

        $stone->update($request->all());

        return redirect()->route('stones.index')->with('success', 'Stone updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stone $stone)
    {
        $stone->delete();

        return redirect()->route('stones.index')->with('success', 'Stone deleted successfully.');
    }
}
