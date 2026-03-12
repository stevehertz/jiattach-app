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
        Schema::create('application_histories', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Who performed the action
            $table->unsignedBigInteger('student_id')->nullable(); // Store student_id for easier querying, can be derived from application_id
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();

            // Status tracking
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->string('action'); // created, updated, status_changed, interview_scheduled, offer_sent, etc.

            // Additional data
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Store additional context like interview details, offer details, etc.

            // IP and user agent for audit
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->nullOnDelete();

             // Indexes for faster queries
            $table->index(['application_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('new_status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_histories');
    }
};
