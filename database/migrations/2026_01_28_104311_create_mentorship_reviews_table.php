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
        Schema::create('mentorship_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mentorship_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('mentorship_session_id')->nullable()->constrained('mentorship_sessions')->onDelete('cascade');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewee_id')->nullable()->constrained('users')->onDelete('cascade');

            // Review details
            $table->string('review_type')->nullable(); // mentor_to_mentee, mentee_to_mentor, mutual, system
            $table->decimal('overall_rating', 3, 2)->nullable();
            $table->text('overall_comment')->nullable();

            // Category ratings
            $table->decimal('knowledge_rating', 3, 2)->nullable();
            $table->decimal('communication_rating', 3, 2)->nullable();
            $table->decimal('preparation_rating', 3, 2)->nullable();
            $table->decimal('engagement_rating', 3, 2)->nullable();
            $table->decimal('value_rating', 3, 2)->nullable();
            $table->decimal('professionalism_rating', 3, 2)->nullable();
            $table->decimal('recommendation_rating', 3, 2)->nullable();

            // Text fields
            $table->json('strengths')->nullable();
            $table->json('areas_for_improvement')->nullable();
            $table->text('key_takeaways')->nullable();
            $table->text('suggestions')->nullable();

            // Goals
            $table->boolean('goals_achieved')->default(false);
            $table->text('goals_comment')->nullable();
            $table->json('achieved_goals')->nullable();
            $table->json('pending_goals')->nullable();

            // Session specific
            $table->boolean('is_session_review')->default(false);
            $table->text('session_specific_feedback')->nullable();
            $table->boolean('session_met_expectations')->default(true);

            // Visibility & anonymity
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_response')->default(true);

            // Response
            $table->text('response')->nullable();
            $table->timestamp('responded_at')->nullable();

            // STATUS FIELD - This is what's missing
            $table->string('status')->default('draft'); // draft, submitted, published, flagged, hidden, archived

            // Flagging
            $table->string('flag_reason')->nullable();
            $table->foreignId('flagged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('flagged_at')->nullable();

            // Helpful votes
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->json('helpful_users')->nullable();
            $table->json('not_helpful_users')->nullable();

            // Verification
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();

            // Moderation
            $table->boolean('requires_moderation')->default(false);
            $table->text('moderation_notes')->nullable();

            // Timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->integer('edit_count')->default(0);

            // Context
            $table->string('relationship_status_at_review')->nullable(); // active, completed, cancelled, paused
            $table->integer('weeks_into_mentorship')->nullable();
            $table->integer('sessions_completed_at_review')->nullable();
            $table->json('tags')->nullable();
            $table->string('reviewer_role')->nullable();
            $table->string('reviewee_role')->nullable();
            $table->text('additional_context')->nullable();

            // Add soft deletes if not already present
            if (!Schema::hasColumn('mentorship_reviews', 'deleted_at')) {
                $table->softDeletes();
            }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorship_reviews');
    }
};
