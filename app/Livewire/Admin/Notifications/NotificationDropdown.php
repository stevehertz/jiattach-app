<?php

namespace App\Livewire\Admin\Notifications;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $showDropdown = false;

    protected $listeners = [
        'refreshNotifications' => 'loadNotifications'
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (Auth::check()) {
            $user = User::find(Auth::id());
            // Get latest 10 notifications
            $this->notifications = $user
                ->notifications()
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->data['title'] ?? 'Notification',
                        'message' => $notification->data['message'] ?? '',
                        'type' => $notification->data['type'] ?? 'system',
                        'icon' => $notification->data['icon'] ?? 'fas fa-bell',
                        'url' => $notification->data['url'] ?? '#',
                        'data' => $notification->data['data'] ?? [],
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at,
                        'time_ago' => $notification->created_at->diffForHumans(),
                    ];
                });

            // Get unread count
            $this->unreadCount = $user
                ->unreadNotifications()
                ->count();
        }
    }

    public function markAsRead($notificationId)
    {
        $user = User::find(Auth::id());
        $notification = $user
            ->notifications()
            ->find($notificationId);

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            $this->loadNotifications();

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Notification marked as read'
            ]);
        }
    }

    public function markAllAsRead()
    {
        $user = User::find(Auth::id());
        $user->unreadNotifications()->update(['read_at' => now()]);
        $this->loadNotifications();
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'All notifications marked as read'
        ]);
    }

    public function getNotificationIcon($type)
    {
        $icons = [
            'student_registration' => 'fas fa-user-graduate text-success',
            'placement_match' => 'fas fa-handshake text-primary',
            'placement_offered' => 'fas fa-envelope-open-text text-info',
            'placement_confirmed' => 'fas fa-check-circle text-success',
            'mentorship_session' => 'fas fa-calendar-alt text-warning',
            'system_alert' => 'fas fa-exclamation-triangle text-danger',
            'document_approved' => 'fas fa-file-alt text-success',
            'document_rejected' => 'fas fa-file-alt text-danger',
            'default' => 'fas fa-bell text-secondary',
        ];

        return $icons[$type] ?? $icons['default'];
    }

    public function render()
    {
        return view('livewire.admin.notifications.notification-dropdown');
    }
}
