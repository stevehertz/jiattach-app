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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
             $table->string('gateway'); // mpesa, pesapal
            $table->string('transaction_id')->unique(); // Gateway transaction ID
            $table->string('merchant_reference')->unique(); // Your reference
            $table->foreignId('user_id')->nullable()->constrained();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('KES');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('account_reference')->nullable(); // Order/Invoice number
            $table->string('transaction_desc')->nullable();
            
            $table->enum('status', [
                'pending', 'processing', 'completed', 'failed', 
                'cancelled', 'refunded'
            ])->default('pending');
            
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->json('callback_payload')->nullable();
            
            $table->string('mpesa_receipt')->nullable(); // M-Pesa receipt number
            $table->string('pesapal_tracking_id')->nullable(); // Pesapal tracking ID
            $table->string('payment_method')->nullable(); // M-Pesa, Card, Airtel Money
            
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['merchant_reference', 'gateway']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
