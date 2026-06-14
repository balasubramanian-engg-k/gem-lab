<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentFailuresTable extends Migration
{
    public function up()
    {
        Schema::create('payment_failures', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('error_code')->nullable();
            $table->text('error_description')->nullable();
            $table->string('error_source')->nullable();
            $table->string('error_step')->nullable();
            $table->string('error_reason')->nullable();
            $table->json('payload')->nullable(); // Store the full webhook payload
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_failures');
    }
}

