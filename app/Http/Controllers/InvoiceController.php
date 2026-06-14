<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Stone;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\InvoiceDeliveryNoteExport;
use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Sorting functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }
        
        // Always use withCount for product_count column availability
        $query = Invoice::with('invoiceDetails', 'productType')->withCount('invoiceDetails');
        
        // Search functionality (use qualified column names to avoid ambiguity when joins are added)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoices.customer_name', 'like', '%' . $search . '%')
                  ->orWhere('invoices.location', 'like', '%' . $search . '%')
                  ->orWhere('invoices.assignee_name', 'like', '%' . $search . '%')
                  ->orWhere('invoices.status', 'like', '%' . $search . '%')
                  ->orWhere('invoices.id', 'like', '%' . $search . '%')
                  ->orWhereHas('productType', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Handle different sort columns
        switch ($sortBy) {
            case 'id':
                $query->orderBy('invoices.id', $sortOrder);
                break;
            case 'customer_name':
                $query->orderBy('customer_name', $sortOrder);
                break;
            case 'location':
                $query->orderBy('location', $sortOrder);
                break;
            case 'product_type':
                $query->leftJoin('product_types', 'invoices.product_type_id', '=', 'product_types.id')
                      ->orderBy('product_types.name', $sortOrder)
                      ->select('invoices.*');
                break;
            case 'total_count':
                $query->orderBy('total_count', $sortOrder);
                break;
            case 'assignee_name':
                $query->orderBy('assignee_name', $sortOrder);
                break;
            case 'product_count':
                $query->orderBy('invoice_details_count', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            case 'status':
                $query->orderBy('status', $sortOrder);
                break;
            case 'delivered_date':
                $query->orderBy('delivered_date', $sortOrder);
                break;
            case 'due_date':
                $query->orderBy('due_date', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $invoices = $query->paginate(20)->withQueryString();

        // Retain search/filters when returning from create or edit
        session(['invoices_index_query' => $request->query()]);

        return view('invoices.index', compact('user', 'invoices', 'sortBy', 'sortOrder'));
    }

    /**
     * Export invoices to Excel (same columns and filters as index).
     */
    public function exportToExcel(Request $request)
    {
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query = Invoice::with('invoiceDetails', 'productType')->withCount('invoiceDetails');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoices.customer_name', 'like', '%' . $search . '%')
                    ->orWhere('invoices.location', 'like', '%' . $search . '%')
                    ->orWhere('invoices.assignee_name', 'like', '%' . $search . '%')
                    ->orWhere('invoices.status', 'like', '%' . $search . '%')
                    ->orWhere('invoices.id', 'like', '%' . $search . '%')
                    ->orWhereHas('productType', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        switch ($sortBy) {
            case 'id':
                $query->orderBy('invoices.id', $sortOrder);
                break;
            case 'customer_name':
                $query->orderBy('customer_name', $sortOrder);
                break;
            case 'location':
                $query->orderBy('location', $sortOrder);
                break;
            case 'product_type':
                $query->leftJoin('product_types', 'invoices.product_type_id', '=', 'product_types.id')
                    ->orderBy('product_types.name', $sortOrder)
                    ->select('invoices.*');
                break;
            case 'total_count':
                $query->orderBy('total_count', $sortOrder);
                break;
            case 'assignee_name':
                $query->orderBy('assignee_name', $sortOrder);
                break;
            case 'product_count':
                $query->orderBy('invoice_details_count', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            case 'status':
                $query->orderBy('status', $sortOrder);
                break;
            case 'delivered_date':
                $query->orderBy('delivered_date', $sortOrder);
                break;
            case 'due_date':
                $query->orderBy('due_date', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $invoices = $query->get();
        $filename = 'invoices_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new InvoicesExport($invoices), $filename);
    }

    public function create()
    {
        $stones = Stone::all();
        $productTypes = ProductType::all();
        
        return view('invoices.create', compact('stones', 'productTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:' . implode(',', Invoice::STATUSES),
            'total_count' => 'nullable|integer|min:0',
            'actual_silver_weight' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'due_date' => 'nullable|date',
            'silver_rate' => 'nullable|numeric|min:0',
            'assignee_name' => 'nullable|string|max:255',
            'stone_cost' => 'nullable|numeric|min:0',
            'wastage_making_certification_cost' => 'required|numeric|min:0',
            'product_type_id' => 'required|exists:product_types,id',
            'products' => 'required|array|min:1',
            'products.*.stone' => 'required|exists:stones,id',
            'products.*.ring_size' => 'nullable|string|max:255',
            'products.*.stone_weight' => 'required|numeric|min:0',
            'products.*.gross_weight' => 'required|numeric|min:0',
        ]);

        // Set delivered_date only when status is DELIVERED
        $deliveredDate = null;
        if ($request->status === 'DELIVERED') {
            $deliveredDate = now()->toDateString();
        }

        // Create invoice
        $invoice = Invoice::create([
            'customer_name' => $request->customer_name,
            'location' => $request->location,
            'status' => $request->status,
            'delivered_date' => $deliveredDate,
            'total_count' => $request->total_count,
            'actual_silver_weight' => $request->actual_silver_weight,
            'remarks' => $request->remarks,
            'due_date' => $request->due_date ? \Carbon\Carbon::parse($request->due_date)->format('Y-m-d') : null,
            'silver_rate' => $request->silver_rate !== null && $request->silver_rate !== '' ? $request->silver_rate : null,
            'assignee_name' => $request->assignee_name,
            'stone_cost' => $request->stone_cost,
            'wastage_making_certification_cost' => $request->wastage_making_certification_cost,
            'product_type_id' => $request->product_type_id,
        ]);

        // Use wastage_making_certification_cost from form
        $makingChargePerProduct = ($request->wastage_making_certification_cost / count($request->products));

        // Create invoice details with incremental product_sl_no starting from 1 for each invoice
        $productSlNo = 1;
        foreach ($request->products as $product) {
            $stone = Stone::find($product['stone']);
            
            // Silver cost is no longer used, set to 0
            $silvercost = 0;
            
            // Use stone rate_per_piece directly as stonecost (no calculation)
            $stonecost = $stone->rate_per_piece;
            
            // Calculate rate = Silvercost + Stonecost + wastage+making+certification cost (from form, distributed per product)
            $rate = $silvercost + $stonecost + $makingChargePerProduct;

            InvoiceDetail::create([
                'product_sl_no' => $productSlNo,
                'product_name' => $product['product_name'] ?? null,
                'invoice_id' => $invoice->id,
                'stone' => $product['stone'],
                'ring_size' => $product['ring_size'] ?? null,
                'ston_weight' => $product['stone_weight'],
                'gross_weight' => $product['gross_weight'],
                'silvercost' => round($silvercost, 2),
                'stonecost' => round($stonecost, 2),
                'making_charge' => round($makingChargePerProduct, 2),
                'rate' => round($rate, 2),
            ]);
            
            // Increment product_sl_no for next product in this invoice
            $productSlNo++;
        }

        $invoice->syncSilverStockUsage();

        return redirect()->route('invoices.index', session('invoices_index_query', []))->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['invoiceDetails.stoneData', 'productType']);
        return view('invoices.show', compact('invoice'));
    }

    public function changelog(Invoice $invoice)
    {
        // Check if user is admin
        // if (!Auth::check() || !Auth::user()->is_admin) {
        //     abort(403, 'Unauthorized access. Only administrators can view changelog.');
        // }
        
        $changelogs = $invoice->changelogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('invoices.changelog', compact('invoice', 'changelogs'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['invoiceDetails.stoneData']);
        $stones = Stone::all();
        $productTypes = ProductType::all();
        
        return view('invoices.edit', compact('invoice', 'stones', 'productTypes'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'status' => 'required|string|in:' . implode(',', Invoice::STATUSES),
            'total_count' => 'required|integer|min:0',
            'actual_silver_weight' => 'required|numeric|min:0',
            'remarks' => 'required|string',
            'due_date' => 'nullable|date',
            'silver_rate' => 'nullable|numeric|min:0',
            'assignee_name' => 'required|string|max:255',
            'stone_cost' => 'nullable|numeric|min:0',
            'wastage_making_certification_cost' => 'required|numeric|min:0',
            'product_type_id' => 'required|exists:product_types,id',
            'products' => 'required|array|min:1',
            'products.*.stone' => 'required|exists:stones,id',
            'products.*.ring_size' => 'nullable|string|max:255',
            'products.*.stone_weight' => 'required|numeric|min:0',
            'products.*.gross_weight' => 'required|numeric|min:0',
        ]);

        // Set delivered_date only when status is DELIVERED
        $deliveredDate = $invoice->delivered_date; // Keep existing date if already set
        if ($request->status === 'DELIVERED' && !$deliveredDate) {
            $deliveredDate = now()->toDateString();
        }

        // Update invoice fields
        $invoice->update([
            'customer_name' => $request->customer_name,
            'location' => $request->location,
            'status' => $request->status,
            'delivered_date' => $deliveredDate,
            'total_count' => $request->total_count,
            'actual_silver_weight' => $request->actual_silver_weight,
            'remarks' => $request->remarks,
            'due_date' => $request->due_date ? \Carbon\Carbon::parse($request->due_date)->format('Y-m-d') : null,
            'silver_rate' => $request->silver_rate !== null && $request->silver_rate !== '' ? $request->silver_rate : null,
            'assignee_name' => $request->assignee_name,
            'stone_cost' => $request->stone_cost,
            'wastage_making_certification_cost' => $request->wastage_making_certification_cost,
            'product_type_id' => $request->product_type_id,
        ]);

        // Use wastage_making_certification_cost from form
        $makingChargePerProduct = ($request->wastage_making_certification_cost / count($request->products));

        // Delete existing invoice details
        $invoice->invoiceDetails()->delete();

        // Create new invoice details with incremental product_sl_no starting from 1
        $productSlNo = 1;
        foreach ($request->products as $product) {
            $stone = Stone::find($product['stone']);
            
            // Silver cost is no longer used, set to 0
            $silvercost = 0;
            
            // Use stone rate_per_piece directly as stonecost (no calculation)
            $stonecost = $stone->rate_per_piece;
            
            // Calculate rate = Silvercost + Stonecost + wastage+making+certification cost (from form, distributed per product)
            $rate = $silvercost + $stonecost + $makingChargePerProduct;

            InvoiceDetail::create([
                'product_sl_no' => $productSlNo,
                'product_name' => $product['product_name'] ?? null,
                'invoice_id' => $invoice->id,
                'stone' => $product['stone'],
                'ring_size' => $product['ring_size'] ?? null,
                'ston_weight' => $product['stone_weight'],
                'gross_weight' => $product['gross_weight'],
                'silvercost' => round($silvercost, 2),
                'stonecost' => round($stonecost, 2),
                'making_charge' => round($makingChargePerProduct, 2),
                'rate' => round($rate, 2),
            ]);
            
            // Increment product_sl_no for next product in this invoice
            $productSlNo++;
        }

        $invoice->syncSilverStockUsage();

        return redirect()->route('invoices.index', session('invoices_index_query', []))->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        // Delete invoice details (cascade should handle this, but being explicit)
        $invoice->invoiceDetails()->delete();
        
        // Delete invoice
        $invoice->delete();

        return redirect()->route('invoices.index', session('invoices_index_query', []))->with('success', 'Invoice deleted successfully.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['invoiceDetails.stoneData', 'productType']);
        
        // Calculate totals
        $totalSilverWeight = $invoice->invoiceDetails->sum('gross_weight') ?? 0;
        $totalStoneCost = $invoice->invoiceDetails->sum('stonecost') ?? 0;
        $totalMakingCharge = $invoice->invoiceDetails->sum('making_charge') ?? 0;
        $totalRate = $invoice->invoiceDetails->sum('rate') ?? 0;
        
        $data = [
            'invoice' => $invoice,
            'totalSilverWeight' => $totalSilverWeight,
            'totalStoneCost' => $totalStoneCost,
            'totalMakingCharge' => $totalMakingCharge,
            'totalRate' => $totalRate,
        ];
        
        $pdf = Pdf::loadView('invoices.pdf', $data);
        $pdf->setOption('enable-html5-parser', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $invoiceNumber = 'AD' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT);
        
        return $pdf->download("Invoice_{$invoiceNumber}.pdf");
    }

    /**
     * Delivery note as Excel — same content/figures as invoices.pdf (full invoice, one file per download).
     */
    public function downloadXls(Invoice $invoice)
    {
        $invoice->load(['invoiceDetails.stoneData', 'productType']);

        $invoiceNumber = 'AD'.str_pad((string) $invoice->id, 6, '0', STR_PAD_LEFT);

        return Excel::download(
            new InvoiceDeliveryNoteExport($invoice),
            "Invoice_{$invoiceNumber}.xlsx"
        );
    }

    public function toggleSilverCost(Invoice $invoice)
    {
        $invoice->toggle_silver_cost = !$invoice->toggle_silver_cost;
        $invoice->save();
        
        return response()->json([
            'success' => true,
            'toggle_silver_cost' => $invoice->toggle_silver_cost
        ]);
    }

    public function toggleSilverRate(Invoice $invoice)
    {
        $invoice->toggle_silver_rate = !$invoice->toggle_silver_rate;
        $invoice->save();

        return response()->json([
            'success' => true,
            'toggle_silver_rate' => $invoice->toggle_silver_rate,
        ]);
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|string|in:' . implode(',', Invoice::STATUSES),
        ]);

        $oldStatus = $invoice->status;
        $newStatus = $request->status;

        // Set delivered_date only when status is DELIVERED
        $deliveredDate = $invoice->delivered_date; // Keep existing date if already set
        if ($newStatus === 'DELIVERED' && !$deliveredDate) {
            $deliveredDate = now()->toDateString();
        }

        $invoice->status = $newStatus;
        $invoice->delivered_date = $deliveredDate;
        $invoice->save();

        return response()->json([
            'success' => true,
            'status' => $invoice->status,
            'delivered_date' => $invoice->delivered_date ? \Carbon\Carbon::parse($invoice->delivered_date)->format('d-m-Y') : null,
            'old_status' => $oldStatus,
        ]);
    }
}
