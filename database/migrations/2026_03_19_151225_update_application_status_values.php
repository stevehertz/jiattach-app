<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
          // Update applications table
        DB::table('applications')
            ->where('status', 'Pending')
            ->update(['status' => 'pending']);

        DB::table('applications')
            ->where('status', 'Under Review')
            ->update(['status' => 'under_review']);

        DB::table('applications')
            ->where('status', 'Shortlisted')
            ->update(['status' => 'shortlisted']);

        DB::table('applications')
            ->where('status', 'Interview Scheduled')
            ->update(['status' => 'interview_scheduled']);

        DB::table('applications')
            ->where('status', 'Interview Completed')
            ->update(['status' => 'interview_completed']);

        DB::table('applications')
            ->where('status', 'Offer Sent')
            ->update(['status' => 'offer_sent']);

        DB::table('applications')
            ->where('status', 'Offer Accepted')
            ->update(['status' => 'offer_accepted']);

        DB::table('applications')
            ->where('status', 'Offer Rejected')
            ->update(['status' => 'offer_rejected']);

        DB::table('applications')
            ->where('status', 'Hired')
            ->update(['status' => 'hired']);

        DB::table('applications')
            ->where('status', 'Rejected')
            ->update(['status' => 'rejected']);

        // Also update application_histories table if it exists
        if (Schema::hasTable('application_histories')) {
            DB::table('application_histories')
                ->where('old_status', 'Pending')
                ->update(['old_status' => 'pending']);

            DB::table('application_histories')
                ->where('new_status', 'Pending')
                ->update(['new_status' => 'pending']);

            // Repeat for other status values...
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        // Revert changes if needed
        DB::table('applications')
            ->where('status', 'pending')
            ->update(['status' => 'Pending']);
    }
};
