<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('title')->nullable();
            $table->string('father_mother_name');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('state');
            $table->string('district');
            $table->string('email')->unique();
            $table->string('mobile_number');
            $table->string('mother_tongue');
            $table->string('address');
            $table->string('pincode');
            $table->date('date_of_birth');
            $table->date('date_of_birth_registration')->nullable();
            $table->string('fide_id')->nullable();
            $table->string('aicf_id')->nullable();
            $table->enum('player_type', ['Player', 'Arbiter']);
            $table->string('passport_photo')->nullable();
            $table->string('birth_certificate')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('players');
    }
};

