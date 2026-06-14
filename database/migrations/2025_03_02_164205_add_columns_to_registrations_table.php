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
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('middle_name')->nullable();
            $table->string('title')->nullable();
            $table->string('fide_id')->nullable();
            $table->string('aicp_id')->nullable();
            $table->string('player_type')->nullable();
            $table->date('dob_registration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['middle_name', 'title', 'fide_id', 'aicp_id', 'player_type', 'dob_registration']);
        });
    }
};
