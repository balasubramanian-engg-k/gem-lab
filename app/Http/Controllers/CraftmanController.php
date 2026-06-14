<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use Illuminate\Http\Request;

class CraftmanController extends Controller
{
    public function index(Request $request)
    {
        $query = Craftman::query()->orderBy('name');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%");
            });
        }
        $craftmen = $query->paginate(20);
        return view('craftmen.index', compact('craftmen'));
    }

    public function create()
    {
        return view('craftmen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);
        Craftman::create($request->only('name', 'phone'));
        return redirect()->route('craftmen.index')->with('success', 'Craftman added successfully.');
    }

    public function show(Craftman $craftman)
    {
        return view('craftmen.show', compact('craftman'));
    }

    public function edit(Craftman $craftman)
    {
        return view('craftmen.edit', compact('craftman'));
    }

    public function update(Request $request, Craftman $craftman)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);
        $craftman->update($request->only('name', 'phone'));
        return redirect()->route('craftmen.index')->with('success', 'Craftman updated successfully.');
    }

    public function destroy(Craftman $craftman)
    {
        $craftman->delete();
        return redirect()->route('craftmen.index')->with('success', 'Craftman deleted.');
    }
}
