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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->unique(); // 'mpesa' or 'pesapal'
            $table->boolean('is_active')->default(false);
            $table->boolean('is_sandbox')->default(true);
            
            // Common fields
            $table->string('environment')->default('sandbox'); // sandbox/production
            $table->string('callback_url')->nullable();
            $table->string('timeout_url')->nullable();
            $table->text('webhook_secret')->nullable();
            
            // M-Pesa specific fields
            $table->string('mpesa_consumer_key')->nullable();
            $table->string('mpesa_consumer_secret')->nullable();
            $table->string('mpesa_passkey')->nullable();
            $table->string('mpesa_shortcode')->nullable(); // Paybill/Till number
            $table->string('mpesa_till_number')->nullable(); // For Buy Goods
            $table->string('mpesa_paybill')->nullable(); // For Paybill
            $table->enum('mpesa_transaction_type', ['paybill', 'till'])->default('paybill');
            
            // Pesapal specific fields
            $table->string('pesapal_consumer_key')->nullable();
            $table->string('pesapal_consumer_secret')->nullable();
            $table->string('pesapal_ipn_id')->nullable(); // Instant Payment Notification ID
            $table->string('pesapal_currency')->default('KES');
            
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
