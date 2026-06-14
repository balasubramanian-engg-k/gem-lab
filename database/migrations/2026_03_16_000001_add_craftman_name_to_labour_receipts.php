<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('labour_receipts', function (Blueprint $table) {
            $table->string('craftman_name', 255)->nullable()->after('receipt_number');
        });
        Schema::table('labour_receipts', function (Blueprint $table) {
            $table->dropForeign(['craftman_id']);
        });
        DB::statement('ALTER TABLE labour_receipts MODIFY craftman_id BIGINT UNSIGNED NULL');
        Schema::table('labour_receipts', function (Blueprint $table) {
            $table->foreign('craftman_id')->references('id')->on('craftmen')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('labour_receipts', function (Blueprint $table) {
            $table->dropForeign(['craftman_id']);
        });
        DB::statement('ALTER TABLE labour_receipts MODIFY craftman_id BIGINT UNSIGNED NOT NULL');
        Schema::table('labour_receipts', function (Blueprint $table) {
            $table->foreign('craftman_id')->references('id')->on('craftmen')->cascadeOnDelete();
        });
        Schema::table('labour_receipts', function (Blueprint $table) {
            $table->dropColumn('craftman_name');
        });
    }
};
