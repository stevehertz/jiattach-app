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
            $table->string('match_quality')->nullable()->after('match_score');
            $table->json('matched_criteria')->nullable()->after('match_quality');
            $table->json('match_details')->nullable()->after('matched_criteria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            //
             $table->dropColumn(['match_quality', 'matched_criteria', 'match_details']);
        });
    }
};
