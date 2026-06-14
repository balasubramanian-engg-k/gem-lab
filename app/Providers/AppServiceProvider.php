<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\LabourReceipt;
use App\Observers\InvoiceObserver;
use App\Observers\LabourReceiptObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Invoice::observe(InvoiceObserver::class);
        LabourReceipt::observe(LabourReceiptObserver::class);
    }
}
