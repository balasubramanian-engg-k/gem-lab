<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Gem;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gems', function (Blueprint $table) {
            $table->string('weight_type')->default('gross_weight')->after('diamond_weight');
        });

        // Update existing records to set weight_type as 'gross_weight'
        Gem::query()->update(['weight_type' => 'gross_weight']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gems', function (Blueprint $table) {
            $table->dropColumn('weight_type');
        });
    }
};
