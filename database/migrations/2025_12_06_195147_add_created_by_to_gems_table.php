<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Gem;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gems', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('certificate_generated')->constrained('users')->onDelete('set null');
        });

        // Update all existing records to set created_by to admin user
        $adminUser = User::where('is_admin', true)->first();
        if ($adminUser) {
            Gem::query()->update(['created_by' => $adminUser->id]);
        } else {
            // If no admin user exists, use the first user or create a default admin
            $firstUser = User::first();
            if ($firstUser) {
                Gem::query()->update(['created_by' => $firstUser->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gems', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
