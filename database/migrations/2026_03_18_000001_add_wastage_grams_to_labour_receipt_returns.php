<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labour_receipt_returns', function (Blueprint $table) {
            $table->decimal('wastage_grams', 12, 3)->default(0)->after('weight_received');
        });
    }

    public function down(): void
    {
        Schema::table('labour_receipt_returns', function (Blueprint $table) {
            $table->dropColumn('wastage_grams');
        });
    }
};
