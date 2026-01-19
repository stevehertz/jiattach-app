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
        Schema::create('placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('set null');
            $table->string('status')->default('pending')->index(); // pending, processing, placed, completed, rejected
            $table->text('notes')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('duration_days')->nullable();
            $table->string('department')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_contact')->nullable();
            $table->json('requirements')->nullable();
            $table->decimal('stipend', 10, 2)->nullable();
            $table->timestamp('admin_notified_at')->nullable();
            $table->timestamp('student_notified_at')->nullable();
            $table->timestamp('placement_confirmed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

             // Indexes for better performance
            $table->index(['student_id', 'status']);
            $table->index(['admin_id', 'status']);
            $table->index('organization_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placements');
    }
};
