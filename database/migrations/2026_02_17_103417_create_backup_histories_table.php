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
        Schema::create('backup_histories', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_size')->nullable();
            $table->string('disk')->default('local');
            $table->string('status')->default('completed'); // completed, failed, in_progress
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_histories');
    }
};
