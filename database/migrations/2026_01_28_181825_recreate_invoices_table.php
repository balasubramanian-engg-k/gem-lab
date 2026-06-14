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
        Schema::dropIfExists('invoices');
        
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->nullable();
            $table->date('delivered_date')->nullable();
            $table->integer('total_count')->nullable();
            $table->decimal('actual_silver_weight', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('assignee_name')->nullable();
            $table->decimal('stone_cost', 10, 2)->nullable();
            $table->decimal('wastage_making_certification_cost', 10, 2)->nullable();
            $table->boolean('toggle_silver_cost')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
