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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('id');
            $table->decimal('silver_cost', 10, 2)->nullable()->after('customer_name');
            $table->decimal('stone_cost', 10, 2)->nullable()->after('silver_cost');
            $table->decimal('wastage_making_certification_cost', 10, 2)->nullable()->after('stone_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'silver_cost', 'stone_cost', 'wastage_making_certification_cost']);
        });
    }
};
