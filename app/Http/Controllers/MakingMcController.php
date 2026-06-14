<?php

namespace App\Http\Controllers;

use App\Exports\LabourReceiptReturnsExport;
use App\Exports\LabourReceiptsExport;
use App\Models\LabourReceipt;
use App\Models\LabourReceiptChangelog;
use App\Models\LabourReceiptReturn;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MakingMcController extends Controller
{
    public function index(Request $request)
    {
        $query = LabourReceipt::with([
                'productType',
                'creator',
                'returns' => static fn ($q) => $q->orderByDesc('received_at')->orderByDesc('id'),
            ])
            ->withSum('returns as paid_total', 'amount_paid')
            ->withSum('returns as ornament_weight_received', 'weight_received')
            ->withSum('returns as wastage_weight_received', 'wastage_grams')
            ->orderBy('created_at', 'desc');

        if ($request->filled('craftman_name')) {
            $query->where(function ($q) use ($request) {
                $q->where('craftman_name', 'like', '%' . $request->craftman_name . '%')
                    ->orWhereHas('craftman', fn ($q) => $q->where('name', 'like', '%' . $request->craftman_name . '%'));
            });
        }
        if ($request->filled('workflow_status')) {
            $query->where('workflow_status', $request->workflow_status);
        }

        $totalsQuery = clone $query;
        $grandTotals = [
            'count_issued' => (int) (clone $totalsQuery)->sum('count_issued'),
            'count_received' => (int) (clone $totalsQuery)->sum('total_count_received'),
            'weight_issued' => (float) (clone $totalsQuery)->sum('silver_gross_weight'),
            'weight_received' => (float) LabourReceiptReturn::query()
                ->whereIn('labour_receipt_id', (clone $totalsQuery)->select('id'))
                ->selectRaw('COALESCE(SUM(weight_received),0) + COALESCE(SUM(wastage_grams),0) as total')
                ->value('total'),
            'chargable_amount' => (float) (clone $totalsQuery)->sum('amount'),
            'paid_amount' => (float) LabourReceiptReturn::query()
                ->whereIn('labour_receipt_id', (clone $totalsQuery)->select('id'))
                ->sum('amount_paid'),
        ];

        $receipts = $query->paginate(20)->withQueryString();

        return view('making-mc.index', compact('receipts', 'grandTotals'));
    }

    public function exportReceipts(Request $request)
    {
        $query = LabourReceipt::with(['productType', 'creator'])
            ->withSum('returns as paid_total', 'amount_paid')
            ->withSum('returns as ornament_weight_received', 'weight_received')
            ->withSum('returns as wastage_weight_received', 'wastage_grams')
            ->orderBy('created_at', 'desc');
        if ($request->filled('craftman_name')) {
            $query->where(function ($q) use ($request) {
                $q->where('craftman_name', 'like', '%' . $request->craftman_name . '%')
                    ->orWhereHas('craftman', fn ($q) => $q->where('name', 'like', '%' . $request->craftman_name . '%'));
            });
        }
        if ($request->filled('workflow_status')) {
            $query->where('workflow_status', $request->workflow_status);
        }
        $receipts = $query->get();
        $filename = 'labour_receipts_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new LabourReceiptsExport($receipts), $filename);
    }

    public function create()
    {
        $productTypes = ProductType::orderBy('name')->get();
        return view('making-mc.create', compact('productTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'craftman_name' => 'required|string|max:255',
            'product_type_id' => 'required|exists:product_types,id',
            'count_issued' => 'required|integer|min:1',
            'silver_gross_weight' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $receipt = LabourReceipt::create([
            'user_id' => Auth::id(),
            'craftman_id' => null,
            'craftman_name' => $request->craftman_name,
            'product_type_id' => $request->product_type_id,
            'count_issued' => $request->count_issued,
            'silver_gross_weight' => $request->silver_gross_weight,
            'amount' => $request->amount,
            'workflow_status' => LabourReceipt::STATUS_ISSUED,
            'remarks' => $request->remarks,
        ]);

        $receipt->update(['receipt_number' => 'ADCR' . str_pad((string) $receipt->id, 6, '0', STR_PAD_LEFT)]);

        return redirect()->route('making-mc.show', $receipt)->with('success', 'Labour receipt created. MC Id: ' . $receipt->mc_id);
    }

    public function show(LabourReceipt $making_mc)
    {
        $making_mc->load(['craftman', 'productType', 'returns', 'creator'])
            ->loadSum('returns as ornament_weight_received', 'weight_received')
            ->loadSum('returns as wastage_weight_received', 'wastage_grams');
        return view('making-mc.show', ['receipt' => $making_mc]);
    }

    public function changelog(LabourReceipt $making_mc)
    {
        $changelogs = $making_mc->changelogs()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('making-mc.changelog', ['receipt' => $making_mc, 'changelogs' => $changelogs]);
    }

    public function edit(LabourReceipt $making_mc)
    {
        $productTypes = ProductType::orderBy('name')->get();
        return view('making-mc.edit', ['receipt' => $making_mc, 'productTypes' => $productTypes]);
    }

    public function update(Request $request, LabourReceipt $making_mc)
    {
        $request->validate([
            'craftman_name' => 'required|string|max:255',
            'product_type_id' => 'required|exists:product_types,id',
            'count_issued' => 'required|integer|min:1',
            'silver_gross_weight' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $making_mc->update([
            'craftman_id' => null,
            'craftman_name' => $request->craftman_name,
            'product_type_id' => $request->product_type_id,
            'count_issued' => $request->count_issued,
            'silver_gross_weight' => $request->silver_gross_weight,
            'amount' => $request->amount,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('making-mc.show', $making_mc)->with('success', 'Order updated.');
    }

    /**
     * Show delete confirmation screen (only when order has no returns).
     */
    public function delete(LabourReceipt $making_mc)
    {
        if ($making_mc->returns()->exists()) {
            return redirect()->route('making-mc.show', $making_mc)->with('error', 'Cannot delete order that has returns. Delete returns first or keep the order.');
        }
        return view('making-mc.delete', ['receipt' => $making_mc]);
    }

    public function destroy(LabourReceipt $making_mc)
    {
        if ($making_mc->returns()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete order that has returns. Delete returns first or keep the order.');
        }
        $making_mc->delete();
        return redirect()->route('making-mc.index')->with('success', 'Order deleted.');
    }

    public function createReturn(LabourReceipt $making_mc)
    {
        return view('making-mc.return-create', ['receipt' => $making_mc]);
    }

    public function storeReturn(Request $request, LabourReceipt $making_mc)
    {
        $request->validate([
            'count_received' => 'required|integer|min:0',
            'weight_received' => 'nullable|numeric|min:0',
            'wastage_grams' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'received_at' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        LabourReceiptReturn::create([
            'labour_receipt_id' => $making_mc->id,
            'count_received' => $request->count_received,
            'weight_received' => $request->weight_received ?? 0,
            'wastage_grams' => $request->wastage_grams,
            'amount_paid' => $request->amount_paid,
            'received_at' => $request->received_at,
            'remarks' => $request->remarks,
        ]);

        $making_mc->refreshTotalsAndStatus();

        LabourReceiptChangelog::create([
            'labour_receipt_id' => $making_mc->id,
            'user_id' => Auth::id(),
            'action' => 'return_added',
            'description' => sprintf(
                'Return added: count %s, weight %s g, wastage %s g, amount paid %s, date %s',
                $request->count_received,
                number_format((float) ($request->weight_received ?? 0), 3),
                number_format((float) $request->wastage_grams, 3),
                number_format((float) $request->amount_paid, 2),
                \Carbon\Carbon::parse($request->received_at)->format('d-m-Y')
            ),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('making-mc.show', $making_mc)->with('success', 'Return recorded.');
    }

    public function destroyReturn(LabourReceipt $making_mc, LabourReceiptReturn $labour_receipt_return)
    {
        if ($labour_receipt_return->labour_receipt_id != $making_mc->id) {
            abort(404);
        }
        $desc = sprintf(
            'Return removed: count %s, date %s',
            $labour_receipt_return->count_received,
            $labour_receipt_return->received_at->format('d-m-Y')
        );
        $labour_receipt_return->delete();
        $making_mc->refreshTotalsAndStatus();

        LabourReceiptChangelog::create([
            'labour_receipt_id' => $making_mc->id,
            'user_id' => Auth::id(),
            'action' => 'return_deleted',
            'description' => $desc,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('making-mc.show', $making_mc)->with('success', 'Return deleted.');
    }

    public function exportReturns(LabourReceipt $making_mc)
    {
        $returns = $making_mc->returns()->orderBy('received_at')->get();
        $filename = 'returns_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $making_mc->mc_id) . '_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new LabourReceiptReturnsExport($returns, $making_mc->mc_id), $filename);
    }
}
