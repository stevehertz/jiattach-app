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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('boolean'); // boolean, select, multi-select, text
            $table->json('options')->nullable(); // For select/multi-select types
            $table->json('value')->nullable();
            $table->string('category')->default('general'); // general, email, sms, push, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_system')->default(false); // System settings can't be deleted
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
