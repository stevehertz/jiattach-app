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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // The Student
            $table->foreignId('attachment_opportunity_id')->constrained()->cascadeOnDelete();
            
            $table->decimal('match_score', 5, 2)->default(0); // 0-100%
            $table->text('cover_letter')->nullable();
            
            // Status Flow: pending -> reviewing -> shortlisted -> offered -> accepted/rejected
            $table->string('status')->default('pending');
            $table->text('employer_notes')->nullable();
            
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
