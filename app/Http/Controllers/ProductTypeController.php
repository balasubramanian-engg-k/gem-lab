<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ProductType::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        $productTypes = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('product-types.index', compact('productTypes', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_types,name',
        ]);

        ProductType::create($request->all());

        return redirect()->route('product-types.index')->with('success', 'Product type added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductType $productType)
    {
        return view('product-types.show', compact('productType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductType $productType)
    {
        return view('product-types.edit', compact('productType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductType $productType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_types,name,' . $productType->id,
        ]);

        $productType->update($request->all());

        return redirect()->route('product-types.index')->with('success', 'Product type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductType $productType)
    {
        // Check if product type is being used by any invoices
        if ($productType->invoices()->count() > 0) {
            return redirect()->route('product-types.index')
                ->with('error', 'Cannot delete product type. It is being used by existing invoices.');
        }

        $productType->delete();

        return redirect()->route('product-types.index')->with('success', 'Product type deleted successfully.');
    }
}
