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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_reg_number')->unique();
            $table->string('institution_name');
            $table->enum('institution_type', ['university', 'college', 'polytechnic', 'technical']);
            $table->string('course_name');
            $table->enum('course_level', ['certificate', 'diploma', 'bachelor', 'masters', 'phd']);
            $table->integer('year_of_study');
            $table->integer('expected_graduation_year');
            $table->decimal('cgpa', 3, 2)->nullable();
            $table->json('skills')->nullable(); // JSON array of skills
            $table->json('interests')->nullable(); // JSON array of interests
            $table->string('cv_url')->nullable();
            $table->string('transcript_url')->nullable();
            $table->enum('attachment_status', ['seeking', 'applied', 'interviewing', 'placed', 'completed'])->default('seeking');
            $table->date('attachment_start_date')->nullable();
            $table->date('attachment_end_date')->nullable();
            $table->integer('preferred_attachment_duration')->nullable(); // in months
            $table->string('preferred_location')->nullable();
            $table->timestamps();

            $table->index(['institution_name', 'course_name']);
            $table->index('attachment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
