<?php

namespace App\Livewire\Admin\Notifications;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationsIndex extends Component
{

    use WithPagination;

    public $filter = 'all'; // all, unread, read
    public $selectedNotifications = [];
    public $selectAll = false;

    protected $queryString = ['filter'];

    protected $listeners = ['refreshNotifications' => '$refresh'];

    public function markAsRead($notificationId)
    {
        $user  = User::find(Auth::id());
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Notification marked as read'
            ]);
        }
    }

    public function markAsUnread($notificationId)
    {
        $user  = User::find(Auth::id());
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->update(['read_at' => null]);
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Notification marked as unread'
            ]);
        }
    }

    public function markAllAsRead()
    {
        $user  = User::find(Auth::id());
        $user->unreadNotifications()->update(['read_at' => now()]);
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'All notifications marked as read'
        ]);
    }

    public function markSelectedAsRead()
    {
        $user  = User::find(Auth::id());
        if (!empty($this->selectedNotifications)) {
            $user->notifications()
                ->whereIn('id', $this->selectedNotifications)
                ->update(['read_at' => now()]);

            $count = count($this->selectedNotifications);
            $this->selectedNotifications = [];
            $this->selectAll = false;

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => $count . ' notifications marked as read'
            ]);
        }
    }

    public function deleteNotification($notificationId)
    {
        $user = User::find(Auth::id());
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->delete();
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Notification deleted'
            ]);


            activity_log(
                'Notification deleted',
                'notification_deleted',
                ['notification_id' => $notificationId],
                'notification'
            );
        }
    }

    public function deleteSelected()
    {
        $user = User::find(Auth::id());
        if (!empty($this->selectedNotifications)) {
            $user->notifications()
                ->whereIn('id', $this->selectedNotifications)
                ->delete();

            $count = count($this->selectedNotifications);
            $this->selectedNotifications = [];
            $this->selectAll = false;

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => $count . ' notifications deleted'
            ]);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedNotifications = $this->getNotificationsQuery()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedNotifications = [];
        }
    }

    public function getNotificationsQuery()
    {
        $user = User::find(Auth::id());
        $query = $user->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        return $query->latest();
    }

    public function getNotificationIcon($type)
    {
        $icons = [
            'student_registration' => 'fa-user-graduate text-success',
            'placement_match' => 'fa-handshake text-primary',
            'placement_offered' => 'fa-envelope-open-text text-info',
            'placement_confirmed' => 'fa-check-circle text-success',
            'mentorship_session' => 'fa-calendar-alt text-warning',
            'system_alert' => 'fa-exclamation-triangle text-danger',
            'document_approved' => 'fa-file-alt text-success',
            'document_rejected' => 'fa-file-alt text-danger',
            'default' => 'fa-bell text-secondary',
        ];

        return $icons[$type] ?? $icons['default'];
    }

    public function getNotificationBgClass($notification)
    {
        if (!is_null($notification->read_at)) {
            return '';
        }
        return 'bg-light-blue';
    }


    public function render()
    {
        $user = User::find(Auth::id());
        $notifications = $this->getNotificationsQuery()->paginate(20);
        $unreadCount = $user->unreadNotifications()->count();

        return view('livewire.admin.notifications.notifications-index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
