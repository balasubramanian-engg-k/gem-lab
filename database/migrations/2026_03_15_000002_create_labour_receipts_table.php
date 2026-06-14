<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labour_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 32)->nullable(); // MC Id e.g. ADCR001
            $table->foreignId('craftman_id')->constrained('craftmen')->cascadeOnDelete();
            $table->foreignId('product_type_id')->constrained('product_types')->cascadeOnDelete();
            $table->unsignedInteger('count_issued')->default(0);
            $table->decimal('silver_gross_weight', 12, 3)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('workflow_status', 32)->default('ISSUED'); // ISSUED, PARTIALLY_RECEIVED, FULLY_RECEIVED
            $table->unsignedInteger('total_count_received')->default(0);
            $table->decimal('total_weight_received', 12, 3)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labour_receipts');
    }
};
