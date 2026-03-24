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
        Schema::table('applications', function (Blueprint $table) {
            //
            // Payment transaction relationship
            $table->foreignId('payment_transaction_id')
                ->nullable()
                ->after('organization_id')
                ->constrained('payment_transactions')
                ->onDelete('set null');

            // Payment completion tracking
            $table->timestamp('payment_completed_at')
                ->nullable()
                ->after('decline_feedback');

            // Offer letter generation tracking
            $table->timestamp('offer_letter_generated_at')
                ->nullable()
                ->after('payment_completed_at');

            // Offer letter storage (URL or path)
            $table->string('offer_letter_url')
                ->nullable()
                ->after('offer_letter_generated_at');

            // Payment reference for quick lookup
            $table->string('payment_reference')
                ->nullable()
                ->after('offer_letter_url');

            // Add indexes for faster queries
            $table->index('payment_transaction_id');
            $table->index('payment_completed_at');
            $table->index('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['payment_transaction_id']);
            $table->dropIndex(['payment_completed_at']);
            $table->dropIndex(['payment_reference']);

            // Drop foreign key constraint
            $table->dropForeign(['payment_transaction_id']);

            // Drop columns
            $table->dropColumn([
                'payment_transaction_id',
                'payment_completed_at',
                'offer_letter_generated_at',
                'offer_letter_url',
                'payment_reference',
            ]);
        });
    }
};
