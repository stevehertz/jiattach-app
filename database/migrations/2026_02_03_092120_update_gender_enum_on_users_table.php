<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        DB::statement("
            ALTER TABLE users 
            MODIFY gender ENUM(
                'male',
                'female',
                'other',
                'prefer_not_to_say'
            ) NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::statement("
            ALTER TABLE users 
            MODIFY gender ENUM(
                'male',
                'female',
                'other'
            ) NOT NULL
        ");
    }
};
