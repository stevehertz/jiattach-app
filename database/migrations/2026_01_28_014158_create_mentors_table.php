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
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('job_title');
            $table->string('company');
            $table->integer('years_of_experience');
            $table->json('areas_of_expertise'); // JSON array of expertise areas
            $table->json('industries'); // JSON array of industries
            $table->string('linkedin_profile')->nullable();
            $table->string('twitter_profile')->nullable();
            $table->string('website')->nullable();
            $table->text('bio')->nullable();
            $table->text('mentoring_philosophy')->nullable();
            $table->integer('max_mentees')->default(5);
            $table->integer('current_mentees')->default(0);
            $table->enum('availability', ['available', 'limited', 'fully_booked', 'unavailable'])->default('available');
            $table->enum('mentoring_focus', ['career_development', 'technical_skills', 'leadership', 'entrepreneurship', 'industry_specific', 'general'])->default('career_development');
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->boolean('offers_free_sessions')->default(false);
            $table->integer('free_sessions_per_month')->default(2);
            $table->json('preferred_meeting_times')->nullable(); // JSON array of preferred times
            $table->enum('meeting_preference', ['video', 'phone', 'in_person', 'hybrid'])->default('video');
            $table->integer('session_duration_minutes')->default(60);
            $table->json('languages')->nullable(); // JSON array of languages spoken
            $table->json('certifications')->nullable(); // JSON array of certifications
            $table->json('education_background')->nullable(); // JSON array of education
            $table->integer('total_sessions_conducted')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('successful_mentees')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('featured_at')->nullable();
            $table->timestamps();

              // Indexes
            $table->index(['availability', 'is_verified']);
            $table->index(['mentoring_focus', 'availability']);
            $table->index('is_featured');
            $table->index('average_rating');
            $table->index('years_of_experience');
            $table->index('max_mentees');
            $table->index('current_mentees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentors');
    }
};
