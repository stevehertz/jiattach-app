<?php

use App\Models\User;
use App\Models\UserNotificationPreference;
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
        //
        // Set default preference for existing admin users
        $adminUsers = User::role(['admin', 'super-admin'])->get();

        foreach ($adminUsers as $admin) {
            UserNotificationPreference::updateOrCreate(
                [
                    'user_id' => $admin->id,
                    'notification_type' => 'new_student_registration',
                ],
                [
                    'channels' => ['in_app', 'push'],
                    'is_enabled' => true,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        UserNotificationPreference::where('notification_type', 'new_student_registration')->delete();
    }
};
