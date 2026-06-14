<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('invoice_details');
        
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->string('product_sl_no')->nullable();
            $table->string('product_name')->nullable();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('stone')->nullable()->constrained('stones')->onDelete('set null');
            $table->string('ring_size')->nullable();
            $table->decimal('ston_weight', 10, 2)->nullable();
            $table->decimal('gross_weight', 10, 2)->nullable();
            $table->string('size')->nullable();
            $table->decimal('silvercost', 10, 2)->nullable();
            $table->decimal('stonecost', 10, 2)->nullable();
            $table->decimal('making_charge', 10, 2)->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
