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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->enum('disability_status', [
                'none',
                'mobility',
                'visual',
                'hearing',
                'cognitive',
                'other',
                'prefer_not_to_say'
            ])->nullable()->after('bio');

            $table->text('disability_details')->nullable()->after('disability_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('disability_status');
            $table->dropColumn('disability_details');
        });
    }
};
