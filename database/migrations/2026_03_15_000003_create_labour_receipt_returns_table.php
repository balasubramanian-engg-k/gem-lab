<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labour_receipt_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labour_receipt_id')->constrained('labour_receipts')->cascadeOnDelete();
            $table->unsignedInteger('count_received')->default(0);
            $table->decimal('weight_received', 12, 3)->default(0);
            $table->date('received_at');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labour_receipt_returns');
    }
};
