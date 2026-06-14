<?php

namespace App\Http\Controllers;

use App\Exports\StoneDetailsReportExport;
use App\Models\Invoice;
use App\Models\LabourReceipt;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Report page: customer and product type dropdowns. Form submit returns PDF
     * (single customer or all customers horizontally).
     */
    public function index(Request $request)
    {
        $customerNames = Invoice::query()
            ->select('customer_name')
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->distinct()
            ->orderBy('customer_name')
            ->pluck('customer_name');

        $locations = Invoice::query()
            ->select('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        $productTypes = ProductType::orderBy('name')->get();

        // Form submitted (customer_name is always present in form)
        if ($request->has('customer_name')) {
            return $this->downloadExcel($request);
        }

        return view('report.index', compact('customerNames', 'locations', 'productTypes'));
    }

    /**
     * Build stone-details report data (OPENED invoices; same filters as the report form).
     * Single customer: one column. All customers: one column per customer/location (horizontal).
     */
    private function stoneReportPayload(Request $request): array
    {
        $customerName = $request->customer_name;
        $productTypeId = $request->product_type_id;
        $productTypeName = $productTypeId ? (ProductType::find($productTypeId)?->name) : null;

        $multiCustomer = ($customerName === '' || $customerName === null);

        // Report only considers invoices with status OPENED
        $reportStatus = 'OPENED';
        $locationFilter = $request->location;
        $query = Invoice::query()
            ->select('customer_name', 'location')
            ->where('status', $reportStatus)
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->when($productTypeId, fn ($q) => $q->where('product_type_id', $productTypeId))
            ->when($request->filled('location') && $request->location !== '', function ($q) use ($locationFilter) {
                if ($locationFilter === '__blank__') {
                    $q->where(function ($q) {
                        $q->whereNull('location')->orWhere('location', '');
                    });
                } else {
                    $q->where('location', $locationFilter);
                }
            });
        if (! $multiCustomer) {
            $query->where('customer_name', $customerName);
        }
        $customerLocationPairs = $query->distinct()->orderBy('customer_name')->orderBy('location')->get();

        $columns = [];
        $allStoneNames = collect();

        foreach ($customerLocationPairs as $row) {
            $name = $row->customer_name;
            $location = $row->location ?? '';

            $invoices = Invoice::query()
                ->where('status', $reportStatus)
                ->where('customer_name', $name)
                ->where(function ($q) use ($row) {
                    if ($row->location === null || $row->location === '') {
                        $q->whereNull('location')->orWhere('location', '');
                    } else {
                        $q->where('location', $row->location);
                    }
                })
                ->when($productTypeId, fn ($q) => $q->where('product_type_id', $productTypeId))
                ->with(['invoiceDetails.stoneData'])
                ->orderBy('id')
                ->get();

            $invoiceNumbers = $invoices->map(fn ($inv) => 'AD'.str_pad($inv->id, 6, '0', STR_PAD_LEFT))->all();

            $byStone = [];
            $totalPcs = 0;

            foreach ($invoices as $invoice) {
                foreach ($invoice->invoiceDetails as $detail) {
                    $totalPcs++;
                    $stoneName = $detail->stoneData->stone_name ?? null;
                    if ($stoneName) {
                        if (! isset($byStone[$stoneName])) {
                            $byStone[$stoneName] = [];
                        }
                        $ringSize = $detail->ring_size;
                        if ($ringSize !== null && $ringSize !== '') {
                            $byStone[$stoneName][] = $ringSize;
                        }
                        $allStoneNames->push($stoneName);
                    }
                }
            }

            $columnLabel = $location !== '' ? $name.' ('.$location.')' : $name;
            $columns[] = [
                'name' => $columnLabel,
                'invoiceNumbers' => $invoiceNumbers,
                'customerName' => $name,
                'location' => $location,
                'byStone' => $byStone,
                'totalPcs' => $totalPcs,
            ];
        }

        $stoneRows = $allStoneNames->unique()->sort()->values()->all();

        return [
            'multiCustomer' => $multiCustomer,
            'columns' => $columns,
            'stoneRows' => $stoneRows,
            'customerName' => $multiCustomer ? null : $customerName,
            'byStone' => (! $multiCustomer && count($columns) === 1) ? ($columns[0]['byStone'] ?? []) : null,
            'totalPcs' => (! $multiCustomer && count($columns) === 1) ? ($columns[0]['totalPcs'] ?? 0) : null,
            'productTypeName' => $productTypeName,
        ];
    }

    /**
     * Download the stone-details report as Excel (.xlsx).
     */
    private function downloadExcel(Request $request)
    {
        $data = $this->stoneReportPayload($request);
        $multiCustomer = $data['multiCustomer'];
        $customerName = $request->customer_name;

        $filename = $multiCustomer
            ? 'Report_All_Customers.xls'
            : 'Report_'.preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) $customerName).'.xls';

        return Excel::download(new StoneDetailsReportExport($data), $filename, ExcelFormat::XLS);
    }

    /**
     * Craftman Ledger report: form to enter craftman name, then PDF or HTML view.
     */
    public function craftmanLedger(Request $request)
    {
        return view('report.craftman-ledger');
    }

    /**
     * Craftman Ledger: generate PDF.
     */
    public function craftmanLedgerPdf(Request $request)
    {
        $request->validate(['craftman_name' => 'required|string|max:255']);
        $craftmanName = $request->craftman_name;
        $query = LabourReceipt::with(['craftman', 'productType'])
            ->withSum('returns as ornament_weight_received', 'weight_received')
            ->withSum('returns as wastage_weight_received', 'wastage_grams')
            ->where(function ($q) use ($craftmanName) {
                $q->where('craftman_name', $craftmanName)
                    ->orWhereHas('craftman', fn ($q) => $q->where('name', $craftmanName));
            })
            ->orderBy('created_at');
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        $rows = $query->get();
        $totalIssued = $rows->sum('count_issued');
        $totalReceived = $rows->sum('total_count_received');
        $totalWeightIssued = (float) $rows->sum(fn ($r) => (float) $r->computed_weight_received);
        $totalWeightReceived = (float) $rows->sum(fn ($r) => (float) $r->computed_weight_received);
        $data = [
            'craftmanName' => $craftmanName,
            'rows' => $rows,
            'totalCountIssued' => $totalIssued,
            'totalCountReceived' => $totalReceived,
            'totalWeightIssued' => $totalWeightIssued,
            'totalWeightReceived' => $totalWeightReceived,
        ];
        $pdf = Pdf::loadView('report.craftman-ledger-pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $filename = 'Craftman_Ledger_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $craftmanName) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Craftman Ledger: HTML view (mobile-friendly).
     */
    public function craftmanLedgerView(Request $request)
    {
        $request->validate(['craftman_name' => 'required|string|max:255']);
        $craftmanName = $request->craftman_name;
        $query = LabourReceipt::with(['craftman', 'productType'])
            ->withSum('returns as ornament_weight_received', 'weight_received')
            ->withSum('returns as wastage_weight_received', 'wastage_grams')
            ->where(function ($q) use ($craftmanName) {
                $q->where('craftman_name', $craftmanName)
                    ->orWhereHas('craftman', fn ($q) => $q->where('name', $craftmanName));
            })
            ->orderBy('created_at');
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        $rows = $query->get();
        $totalIssued = $rows->sum('count_issued');
        $totalReceived = $rows->sum('total_count_received');
        $totalWeightIssued = (float) $rows->sum(fn ($r) => (float) $r->computed_weight_received);
        $totalWeightReceived = (float) $rows->sum(fn ($r) => (float) $r->computed_weight_received);
        $pdfUrl = route('report.craftman-ledger.pdf', $request->only(['craftman_name', 'date_from', 'date_to']));
        return view('report.craftman-ledger-view', compact('craftmanName', 'rows', 'totalIssued', 'totalReceived', 'totalWeightIssued', 'totalWeightReceived', 'pdfUrl'));
    }
}
