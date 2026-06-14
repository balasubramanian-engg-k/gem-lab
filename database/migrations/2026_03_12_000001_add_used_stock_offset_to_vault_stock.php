<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vault_stock', function (Blueprint $table) {
            $table->decimal('used_stock_offset', 15, 3)->default(0)->after('amount')
                ->comment('Cumulative add-stock amount applied to reduce displayed used stock');
        });
    }

    public function down(): void
    {
        Schema::table('vault_stock', function (Blueprint $table) {
            $table->dropColumn('used_stock_offset');
        });
    }
};
