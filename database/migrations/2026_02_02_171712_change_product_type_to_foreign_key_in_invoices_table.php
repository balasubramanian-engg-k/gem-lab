<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, ensure product_types table exists and seed it with existing values
        if (Schema::hasTable('product_types')) {
            // Get unique product types from invoices
            $existingTypes = DB::table('invoices')
                ->whereNotNull('product_type')
                ->where('product_type', '!=', '')
                ->distinct()
                ->pluck('product_type')
                ->toArray();

            // Insert existing product types if they don't exist
            foreach ($existingTypes as $type) {
                DB::table('product_types')->insertOrIgnore([
                    'name' => $type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Add new product_type_id column
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('product_type_id')->nullable()->after('wastage_making_certification_cost');
        });

        // Map existing product_type strings to product_type_id
        if (Schema::hasTable('product_types')) {
            $productTypes = DB::table('product_types')->get()->keyBy('name');
            
            DB::table('invoices')->whereNotNull('product_type')->chunkById(100, function ($invoices) use ($productTypes) {
                foreach ($invoices as $invoice) {
                    if (isset($productTypes[$invoice->product_type])) {
                        DB::table('invoices')
                            ->where('id', $invoice->id)
                            ->update(['product_type_id' => $productTypes[$invoice->product_type]->id]);
                    }
                }
            });
        }

        // Drop old product_type column
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });

        // Add foreign key constraint
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('product_type_id')->references('id')->on('product_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['product_type_id']);
        });

        // Add back product_type as string column
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('product_type')->nullable()->after('wastage_making_certification_cost');
        });

        // Map product_type_id back to product_type string
        if (Schema::hasTable('product_types')) {
            $productTypes = DB::table('product_types')->get()->keyBy('id');
            
            DB::table('invoices')->whereNotNull('product_type_id')->chunkById(100, function ($invoices) use ($productTypes) {
                foreach ($invoices as $invoice) {
                    if (isset($productTypes[$invoice->product_type_id])) {
                        DB::table('invoices')
                            ->where('id', $invoice->id)
                            ->update(['product_type' => $productTypes[$invoice->product_type_id]->name]);
                    }
                }
            });
        }

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('product_type_id');
        });
    }
};
