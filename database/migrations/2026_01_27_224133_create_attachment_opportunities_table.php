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
        Schema::create('attachment_opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('responsibilities')->nullable(); // stored as HTML or text
            
            // Logistics
            $table->enum('type', ['attachment', 'internship', 'entry_level'])->default('attachment');
            $table->string('work_type')->default('onsite'); // onsite, remote, hybrid
            $table->string('location')->nullable(); // specific town/branch
            $table->string('county'); // For matching
            
            // Requirements
            $table->decimal('min_gpa', 3, 2)->nullable();
            $table->json('skills_required')->nullable();
            $table->json('courses_required')->nullable(); // Array of course IDs or names
            
            // Duration & Pay
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('duration_months')->default(3);
            $table->decimal('stipend', 10, 2)->nullable(); // Null = Unpaid
            
            // Status
            $table->integer('slots_available')->default(1);
            $table->date('deadline');
            $table->enum('status', ['draft', 'open', 'closed', 'filled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachment_opportunities');
    }
};
