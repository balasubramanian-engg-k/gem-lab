<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to users table
            $table->string('order_id')->unique(); // Razorpay/Stripe Order ID
            $table->string('payment_id')->nullable(); // Payment Gateway Transaction ID
            $table->decimal('amount', 10, 2); // Amount paid
            $table->string('currency')->default('INR'); // Currency
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending'); // Payment status
            $table->string('payment_method')->nullable(); // Card, UPI, Net Banking, etc.
            $table->json('response_data')->nullable(); // Store full response from payment gateway
            $table->timestamp('paid_at')->nullable(); // Timestamp of successful payment
            $table->timestamps(); // Created_at and Updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};

