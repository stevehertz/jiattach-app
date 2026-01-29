<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = User::findOrFail(Auth::user()->id);

         // We use your custom scopeForUser to ensure we get your Model instances
        // allowing us to use getIconAttribute, getTitleAttribute, etc.
        $notifications = Notification::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('student.notifications.index', compact('notifications'));
    }

     /**
     * Mark a specific notification as read and redirect.
     */
    public function markAsRead($id)
    {
        $notification = Notification::forUser(Auth::id())->findOrFail($id);
        
        $notification->markAsRead();

        // Redirect to the target URL if available, otherwise back
        return redirect($notification->url ?? route('student.notifications'));
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        // Use Laravel's native unreadNotifications relationship for bulk update
        // or query your model
        Notification::forUser($user->id)->unread()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
