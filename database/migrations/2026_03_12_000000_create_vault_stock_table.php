<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vault_stock', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 3)->default(0)->comment('Stock in vault (grams)');
            $table->timestamps();
        });

        // Single row: we update this row when user sets "Stock in Vault"
        DB::table('vault_stock')->insert(['amount' => 0, 'created_at' => now(), 'updated_at' => now()]);
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_stock');
    }
};
