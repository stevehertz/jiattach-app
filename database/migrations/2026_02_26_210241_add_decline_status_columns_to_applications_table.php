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
        Schema::table('applications', function (Blueprint $table) {
            //
            $table->timestamp('accepted_at')->nullable()->after('reviewed_at');
            $table->timestamp('declined_at')->nullable()->after('accepted_at');
            $table->string('decline_reason')->nullable()->after('declined_at');
            $table->text('decline_feedback')->nullable()->after('decline_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            //
            $table->dropColumn(['accepted_at', 'declined_at', 'decline_reason', 'decline_feedback']);
        });
    }
};
