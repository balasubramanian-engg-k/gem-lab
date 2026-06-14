<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labour_receipt_returns', function (Blueprint $table) {
            $table->decimal('amount_paid', 12, 2)->default(0)->after('weight_received');
        });
    }

    public function down(): void
    {
        Schema::table('labour_receipt_returns', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
        });
    }
};
