<?php

use App\Enums\InterviewStatus;
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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('scheduled_by')->constrained('users');
            $table->foreignId('interviewer_id')->nullable()->constrained('users');

            // Interview details
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->enum('type', ['online', 'phone', 'in_person'])->default('online');
            $table->string('location')->nullable(); // For in-person
            $table->string('meeting_link')->nullable(); // For online
            $table->string('phone_number')->nullable(); // For phone interviews

            // Status tracking
            $table->enum('status', array_column(InterviewStatus::cases(), 'value'))->default('scheduled');

            // Additional info
            $table->text('notes')->nullable();
            $table->text('feedback')->nullable();
            $table->json('interviewers')->nullable(); // Additional interviewers
            $table->json('documents')->nullable(); // Interview-related documents

            // Timestamps
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('rescheduled_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
             $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
