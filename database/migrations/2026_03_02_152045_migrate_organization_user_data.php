<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing user_id relationships to the pivot table
        $organizations = DB::table('organizations')->whereNotNull('user_id')->get();

        foreach ($organizations as $org) {
            DB::table('organization_user')->insert([
                'organization_id' => $org->id,
                'user_id' => $org->user_id,
                'role' => 'owner', // Assuming existing user_id is the owner
                'is_primary_contact' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        // Get all records from the pivot table that were created during migration
        // We'll identify them by checking against existing organizations
        $pivotRecords = DB::table('organization_user')
            ->join('organizations', 'organization_user.organization_id', '=', 'organizations.id')
            ->whereNotNull('organizations.user_id') // Organizations that originally had user_id
            ->select('organization_user.*')
            ->get();

        foreach ($pivotRecords as $record) {
            // Restore the user_id in organizations table
            DB::table('organizations')
                ->where('id', $record->organization_id)
                ->whereNull('user_id') // Only update if user_id is null
                ->update(['user_id' => $record->user_id]);

            // Optionally, log what was restored
            Log::info('Rollback: Restored user_id for organization', [
                'organization_id' => $record->organization_id,
                'user_id' => $record->user_id
            ]);
        }

        // Delete only the records that were created during the migration
        // This preserves any manually created pivot records that might have been added later
        $deletedCount = DB::table('organization_user')
            ->whereIn('id', $pivotRecords->pluck('id'))
            ->delete();

        // Optionally, output message for console 
        Log::info("Rolled back {$deletedCount} organization_user records and restored original user_id values.");
    }
};
