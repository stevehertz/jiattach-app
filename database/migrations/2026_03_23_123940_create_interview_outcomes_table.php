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
        Schema::create('interview_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained()->onDelete('cascade');
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');

            // Outcome details
            $table->string('outcome'); // from InterviewOutcome enum
            $table->integer('rating')->nullable()->min(1)->max(5);
            $table->text('feedback')->nullable();
            $table->text('notes')->nullable();
            
            // Strengths and areas for improvement
            $table->json('strengths')->nullable();
            $table->json('areas_for_improvement')->nullable();
            
            // Skills assessment
            $table->json('skills_assessment')->nullable();
            
            // Decision details
            $table->string('decision_reason')->nullable();
            $table->dateTime('decision_date')->nullable();
            
            // Follow-up actions
            $table->string('next_steps')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->boolean('follow_up_required')->default(false);
            
            // Additional metadata
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['interview_id', 'outcome']);
            $table->index(['student_id', 'outcome']);
            $table->index(['organization_id', 'outcome']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_outcomes');
    }
};
