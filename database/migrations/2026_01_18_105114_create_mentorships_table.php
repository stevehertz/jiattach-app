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
        Schema::create('mentorships', function (Blueprint $table) {
            $table->id();
             $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mentee_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('goals')->nullable(); // JSON array of goals
            $table->json('topics')->nullable(); // JSON array of topics
            $table->enum('status', [
                'requested',
                'pending_approval',
                'active',
                'paused',
                'completed',
                'cancelled',
                'rejected'
            ])->default('requested');

            $table->enum('meeting_preference', ['video', 'phone', 'in_person', 'hybrid'])->default('video');
            $table->integer('duration_weeks');
            $table->integer('meetings_per_month')->default(2);
            $table->integer('meeting_duration_minutes')->default(60);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('completion_notes')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->boolean('is_paid')->default(false);
            $table->enum('payment_status', ['pending', 'paid', 'overdue', 'cancelled'])->nullable();
            $table->json('availability')->nullable(); // JSON array of available days/times
            $table->text('expectations')->nullable();
            $table->text('mentor_expectations')->nullable();
            $table->text('mentee_expectations')->nullable();
            $table->boolean('is_confidential')->default(true);
            $table->enum('experience_level', ['entry', 'junior', 'mid', 'senior', 'executive'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index(['mentor_id', 'status']);
            $table->index(['mentee_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index('is_paid');
            $table->index('payment_status');

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorships');
    }
};
