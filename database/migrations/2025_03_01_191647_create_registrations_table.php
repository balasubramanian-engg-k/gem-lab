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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('mobilenumber');
            $table->string('parent_name');
            $table->string('relationship');
            $table->string('mother_tounge');
            $table->string('gender');
            $table->date('date_of_birth');
            $table->text('address');
            $table->string('state');
            $table->string('district');
            $table->string('pincode');
            $table->string('photo')->nullable(); // Store file paths
            $table->string('birth_certificate')->nullable(); // Store file paths
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
