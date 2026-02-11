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
        Schema::table('placements', function (Blueprint $table) {
            $table->foreignId('attachment_opportunity_id')
                ->nullable()
                ->after('organization_id')
                ->constrained('attachment_opportunities')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placements', function (Blueprint $table) {
            $table->dropForeignKey(['attachment_opportunity_id']);
            $table->dropColumn('attachment_opportunity_id');
        });
    }
};
