<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('gems', function (Blueprint $table) {
            $table->unique('summary_no');
        });
    }
    
    public function down()
    {
        Schema::table('gems', function (Blueprint $table) {
            $table->dropUnique(['summary_no']);
        });
    }
};
