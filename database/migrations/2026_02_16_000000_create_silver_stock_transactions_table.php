<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('silver_stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'add', 'sell', 'invoice_usage'
            $table->decimal('amount', 12, 3)->default(0); // grams
            $table->date('transaction_date');
            $table->text('remarks')->nullable();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('silver_stock_transactions');
    }
};
