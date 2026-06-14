<?php

namespace App\Http\Controllers;

use App\Models\SilverStockTransaction;
use App\Models\VaultStock;
use App\Exports\StockHistoryExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{
    public function index()
    {
        $vaultStock = VaultStock::current();
        $totalInvoiceUsage = SilverStockTransaction::totalInvoiceUsage();
        $offset = VaultStock::getUsedStockOffset();
        // Used stock: unchanged — invoice usage minus add-stock offset (capped at zero).
        $usedStock = max(0, $totalInvoiceUsage - $offset);
        $currentStock = SilverStockTransaction::currentStock();
        // Remaining: no offset — raw invoice usage only vs add/sell balance.
        $remainingStock = $currentStock - $totalInvoiceUsage;

        return view('stock.index', compact('vaultStock', 'usedStock', 'remainingStock'));
    }

    public function vault()
    {
        $vaultStock = VaultStock::current();
        return view('stock.vault', compact('vaultStock'));
    }

    public function storeVault(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $newAmount = (float) $request->amount;
        $row = VaultStock::first();
        $oldAmount = $row ? (float) $row->amount : 0.0;

        if (!$row) {
            VaultStock::create(['amount' => $newAmount]);
        } else {
            $row->update(['amount' => $newAmount]);
        }

        SilverStockTransaction::create([
            'type' => SilverStockTransaction::TYPE_VAULT_UPDATE,
            'amount' => $newAmount,
            'transaction_date' => now()->toDateString(),
            'remarks' => 'Previous: ' . number_format($oldAmount, 3) . ' g',
            'invoice_id' => null,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('stock.index')->with('success', 'Stock in vault updated successfully.');
    }

    public function add()
    {
        return view('stock.add');
    }

    public function storeAdd(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.001',
            'transaction_date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        SilverStockTransaction::create([
            'type' => SilverStockTransaction::TYPE_ADD,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'remarks' => $request->remarks,
            'user_id' => Auth::id(),
        ]);

        VaultStock::addToUsedStockOffset((float) $request->amount);

        return redirect()->route('stock.index')->with('success', 'Silver stock added successfully.');
    }

    public function sell()
    {
        return view('stock.sell');
    }

    public function storeSell(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.001',
            'transaction_date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        SilverStockTransaction::create([
            'type' => SilverStockTransaction::TYPE_SELL,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'remarks' => $request->remarks,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('stock.index')->with('success', 'Silver sale recorded. Stock deducted.');
    }

    public function editTransaction(SilverStockTransaction $transaction)
    {
        if (! in_array($transaction->type, [SilverStockTransaction::TYPE_ADD, SilverStockTransaction::TYPE_SELL], true)) {
            abort(403, 'Only Add stock and Sell transactions can be edited.');
        }
        return view('stock.transaction-edit', compact('transaction'));
    }

    public function updateTransaction(Request $request, SilverStockTransaction $transaction)
    {
        if (! in_array($transaction->type, [SilverStockTransaction::TYPE_ADD, SilverStockTransaction::TYPE_SELL], true)) {
            abort(403, 'Only Add stock and Sell transactions can be edited.');
        }
        $request->validate([
            'amount' => 'required|numeric|min:0.001',
            'transaction_date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        $oldAmount = (float) $transaction->amount;
        $newAmount = (float) $request->amount;

        $transaction->update([
            'amount' => $newAmount,
            'transaction_date' => $request->transaction_date,
            'remarks' => $request->remarks,
        ]);

        if ($transaction->type === SilverStockTransaction::TYPE_ADD) {
            VaultStock::adjustUsedStockOffset($newAmount - $oldAmount);
        }

        return redirect()->route('stock.history')->with('success', 'Stock transaction updated successfully.');
    }

    public function destroyTransaction(SilverStockTransaction $transaction)
    {
        if ($transaction->type !== SilverStockTransaction::TYPE_ADD) {
            abort(403, 'Only Add stock transactions can be deleted.');
        }
        $amount = (float) $transaction->amount;
        $transaction->delete();
        VaultStock::adjustUsedStockOffset(-$amount);

        return redirect()->route('stock.history')->with('success', 'Add stock transaction deleted.');
    }

    public function history(Request $request)
    {
        $transactions = SilverStockTransaction::with(['invoice', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('stock.history', compact('transactions'));
    }

    public function exportHistory()
    {
        $transactions = SilverStockTransaction::with(['invoice', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'silver_stock_history_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new StockHistoryExport($transactions), $filename);
    }
}
