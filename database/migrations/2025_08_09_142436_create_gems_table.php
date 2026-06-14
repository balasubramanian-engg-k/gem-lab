<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gems', function (Blueprint $table) {
            $table->id();
            $table->string('summary_no');
            $table->string('description');
            $table->string('gross_weight');
            $table->string('diamond_weight');
            $table->string('color');
            $table->string('clarity');
            $table->string('finish');
            $table->string('stone_type'); // required field
            $table->string('shape'); // required field
            $table->string('image')->nullable(); // store image path
            $table->string('comment')->nullable(); // New field
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gems');
    }
};
