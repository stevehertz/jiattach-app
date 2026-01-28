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
        Schema::create('mentorship_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained('mentorships')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('session_type', ['initial', 'regular', 'milestone', 'feedback', 'emergency', 'wrap_up'])->default('regular');
            $table->enum('status', [
                'scheduled',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled',
                'missed',
                'rescheduled'
            ])->default('scheduled');

            // Fix: Use datetime instead of timestamp to avoid strict mode issues
            $table->dateTime('scheduled_start_time');
            $table->dateTime('scheduled_end_time');
            $table->dateTime('actual_start_time')->nullable();
            $table->dateTime('actual_end_time')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->enum('meeting_type', ['video', 'phone', 'in_person', 'hybrid'])->default('video');
            $table->string('meeting_link')->nullable();
            $table->string('meeting_location')->nullable();
            $table->text('agenda')->nullable();
            $table->json('topics_covered')->nullable(); // JSON array of topics
            $table->text('notes')->nullable();
            $table->text('action_items')->nullable();
            $table->text('homework_assigned')->nullable();
            $table->text('mentor_feedback')->nullable();
            $table->text('mentee_feedback')->nullable();
            $table->decimal('mentor_rating', 3, 2)->nullable(); // Rating given by mentee to mentor (1-5)
            $table->decimal('mentee_rating', 3, 2)->nullable(); // Rating given by mentor to mentee (1-5)
            $table->text('mentor_rating_comment')->nullable();
            $table->text('mentee_rating_comment')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->decimal('session_cost', 10, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_reference')->nullable();
            $table->boolean('requires_follow_up')->default(false);
            $table->dateTime('follow_up_date')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->enum('cancelled_by', ['mentor', 'mentee', 'system'])->nullable();
            $table->integer('reschedule_count')->default(0);
            $table->dateTime('reminder_sent_at')->nullable();
            $table->dateTime('follow_up_reminder_sent_at')->nullable();
            $table->json('attachments')->nullable(); // JSON array of attachment URLs
            $table->text('technical_issues')->nullable();
            $table->boolean('was_recorded')->default(false);
            $table->string('recording_url')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('mentorship_id');
            $table->index('status');
            $table->index('session_type');
            $table->index('scheduled_start_time');
            $table->index(['scheduled_start_time', 'status']);
            $table->index('meeting_type');
            $table->index('is_paid');
            $table->index('payment_status');
            $table->index('requires_follow_up');
            $table->index('follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorship_sessions');
    }
};
