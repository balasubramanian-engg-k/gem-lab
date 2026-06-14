<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\GemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StoneController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\CraftmanController;
use App\Http\Controllers\MakingMcController;
Route::get('/', function () {
    return view('home');
});
Route::get('/login', function () {
    return redirect('/');
});
// Custom login URL
Route::get('/gem-admin', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login'); // Keep the name 'login' so Laravel still uses it internally

Route::post('/gem-admin', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');
Route::get('/gem-admin/dashboard', [GemController::class, 'dashboard'])->middleware('auth')->name('dashboard');

Route::get('/gem-admin/gems', [GemController::class, 'index'])->middleware('auth')->name('gems.index');
Route::get('/gem-admin/invoices', [InvoiceController::class, 'index'])->middleware('auth')->name('invoices.index');
Route::get('/gem-admin/invoices/export', [InvoiceController::class, 'exportToExcel'])->middleware('auth')->name('invoices.export');
Route::get('/gem-admin/invoices/create', [InvoiceController::class, 'create'])->middleware('auth')->name('invoices.create');
Route::post('/gem-admin/invoices', [InvoiceController::class, 'store'])->middleware('auth')->name('invoices.store');
Route::get('/gem-admin/invoices/{invoice}', [InvoiceController::class, 'show'])->middleware('auth')->name('invoices.show');
Route::get('/gem-admin/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->middleware('auth')->name('invoices.edit');
Route::put('/gem-admin/invoices/{invoice}', [InvoiceController::class, 'update'])->middleware('auth')->name('invoices.update');
Route::delete('/gem-admin/invoices/{invoice}', [InvoiceController::class, 'destroy'])->middleware('auth')->name('invoices.destroy');
Route::get('/gem-admin/invoices/{invoice}/download-pdf', [InvoiceController::class, 'downloadPdf'])->middleware('auth')->name('invoices.downloadPdf');
Route::get('/gem-admin/invoices/{invoice}/download-xls', [InvoiceController::class, 'downloadXls'])->middleware('auth')->name('invoices.downloadXls');
Route::get('/gem-admin/invoices/{invoice}/changelog', [InvoiceController::class, 'changelog'])->middleware('auth')->name('invoices.changelog');
Route::post('/gem-admin/invoices/{invoice}/toggle-silver-cost', [InvoiceController::class, 'toggleSilverCost'])->middleware('auth')->name('invoices.toggleSilverCost');
Route::post('/gem-admin/invoices/{invoice}/toggle-silver-rate', [InvoiceController::class, 'toggleSilverRate'])->middleware('auth')->name('invoices.toggleSilverRate');
Route::post('/gem-admin/invoices/{invoice}/update-status', [InvoiceController::class, 'updateStatus'])->middleware('auth')->name('invoices.updateStatus');
Route::get('/gem-admin/report', [ReportController::class, 'index'])->middleware('auth', 'admin')->name('report.index');
Route::get('/gem-admin/report/craftman-ledger', [ReportController::class, 'craftmanLedger'])->middleware('auth', 'admin')->name('report.craftman-ledger');
Route::get('/gem-admin/report/craftman-ledger/pdf', [ReportController::class, 'craftmanLedgerPdf'])->middleware('auth', 'admin')->name('report.craftman-ledger.pdf');
Route::get('/gem-admin/report/craftman-ledger/view', [ReportController::class, 'craftmanLedgerView'])->middleware('auth', 'admin')->name('report.craftman-ledger.view');
Route::resource('/gem-admin/craftmen', CraftmanController::class)->middleware('auth', 'admin');
Route::get('/gem-admin/making-mc', [MakingMcController::class, 'index'])->middleware('auth')->name('making-mc.index');
Route::get('/gem-admin/making-mc/export', [MakingMcController::class, 'exportReceipts'])->middleware('auth')->name('making-mc.export');
Route::get('/gem-admin/making-mc/create', [MakingMcController::class, 'create'])->middleware('auth')->name('making-mc.create');
Route::post('/gem-admin/making-mc', [MakingMcController::class, 'store'])->middleware('auth')->name('making-mc.store');
Route::get('/gem-admin/making-mc/{making_mc}/changelog', [MakingMcController::class, 'changelog'])->middleware('auth')->name('making-mc.changelog');
Route::get('/gem-admin/making-mc/{making_mc}', [MakingMcController::class, 'show'])->middleware('auth')->name('making-mc.show');
Route::get('/gem-admin/making-mc/{making_mc}/edit', [MakingMcController::class, 'edit'])->middleware('auth')->name('making-mc.edit');
Route::put('/gem-admin/making-mc/{making_mc}', [MakingMcController::class, 'update'])->middleware('auth')->name('making-mc.update');
Route::get('/gem-admin/making-mc/{making_mc}/delete', [MakingMcController::class, 'delete'])->middleware('auth', 'admin')->name('making-mc.delete');
Route::delete('/gem-admin/making-mc/{making_mc}', [MakingMcController::class, 'destroy'])->middleware('auth', 'admin')->name('making-mc.destroy');
Route::get('/gem-admin/making-mc/{making_mc}/returns/create', [MakingMcController::class, 'createReturn'])->middleware('auth')->name('making-mc.returns.create');
Route::post('/gem-admin/making-mc/{making_mc}/returns', [MakingMcController::class, 'storeReturn'])->middleware('auth')->name('making-mc.returns.store');
Route::get('/gem-admin/making-mc/{making_mc}/returns/export', [MakingMcController::class, 'exportReturns'])->middleware('auth')->name('making-mc.returns.export');
Route::delete('/gem-admin/making-mc/{making_mc}/returns/{labour_receipt_return}', [MakingMcController::class, 'destroyReturn'])->middleware('auth', 'admin')->name('making-mc.returns.destroy');
Route::get('/gem-admin/stock', [StockController::class, 'index'])->middleware('auth', 'admin')->name('stock.index');
Route::get('/gem-admin/stock/add', [StockController::class, 'add'])->middleware('auth', 'admin')->name('stock.add');
Route::post('/gem-admin/stock/add', [StockController::class, 'storeAdd'])->middleware('auth', 'admin')->name('stock.storeAdd');
Route::get('/gem-admin/stock/vault', [StockController::class, 'vault'])->middleware('auth', 'admin')->name('stock.vault');
Route::post('/gem-admin/stock/vault', [StockController::class, 'storeVault'])->middleware('auth', 'admin')->name('stock.storeVault');
Route::get('/gem-admin/stock/sell', [StockController::class, 'sell'])->middleware('auth', 'admin')->name('stock.sell');
Route::post('/gem-admin/stock/sell', [StockController::class, 'storeSell'])->middleware('auth', 'admin')->name('stock.storeSell');
Route::get('/gem-admin/stock/history', [StockController::class, 'history'])->middleware('auth', 'admin')->name('stock.history');
Route::get('/gem-admin/stock/history/export', [StockController::class, 'exportHistory'])->middleware('auth', 'admin')->name('stock.history.export');
Route::get('/gem-admin/stock/transactions/{transaction}/edit', [StockController::class, 'editTransaction'])->middleware('auth', 'admin')->name('stock.transactions.edit');
Route::put('/gem-admin/stock/transactions/{transaction}', [StockController::class, 'updateTransaction'])->middleware('auth', 'admin')->name('stock.transactions.update');
Route::delete('/gem-admin/stock/transactions/{transaction}', [StockController::class, 'destroyTransaction'])->middleware('auth', 'admin')->name('stock.transactions.destroy');
Route::resource('/gem-admin/stones', StoneController::class)->middleware('auth', 'admin');
Route::resource('/gem-admin/product-types', ProductTypeController::class)->middleware('auth', 'admin');
Route::resource('/gem-admin/users', UserController::class)->middleware('auth');
Route::get('/gem-admin/gems/create', [GemController::class, 'create'])->middleware('auth')->name('gems.create');
Route::post('/gem-admin/gems', [GemController::class, 'store'])->middleware('auth')->name('gems.store');
Route::get('/gem-admin/gems/{id}', [GemController::class, 'show'])->name('gems.show');
Route::get('/gem-admin/gems/{gem}/edit', [GemController::class, 'edit'])->name('gems.edit');
Route::get('/search_certificate/{summary_no}', [GemController::class, 'card'])->name('gems.card');
Route::get('/export_certificate/{summary_no}', [GemController::class, 'cardExport'])->name('gems.cardExport');
Route::put('/gem-admin/gems/{gem}', [GemController::class, 'update'])->name('gems.update');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::delete('/gems/{gem}', [GemController::class, 'destroy'])->name('gems.destroy');
Route::get('/certificate/{summary_no}', [GemController::class, 'certificate'])->name('gems.certificate');
Route::get('/gems/export', [GemController::class, 'export'])->name('gems.export');
Route::post('/gems/import', [GemController::class, 'import'])->name('gems.import');
Route::post('/gem-admin/gems/import-csv', [ImportController::class, 'importCsv'])->middleware('auth')->name('gems.importCsv');
Route::post('/gems/download-certificate', [GemController::class, 'downloadCertificate'])->name('download.certificate');
Route::post('/gems/print-certificate', [GemController::class, 'printCertificate'])->name('print.certificate');
Route::get('/gems/{id}/card', [\App\Http\Controllers\GemController::class, 'downloadCard']);
Route::post('/gems/save-certificate', [GemController::class, 'saveCertificate'])->name('save.certificate');
Route::get('/gems/download-zip', [GemController::class, 'downloadZip'])->name('save.downloadZip');
Route::post('/gems/export-gems', [GemController::class, 'exportGems'])->name('gems.export');
require __DIR__.'/auth.php';
