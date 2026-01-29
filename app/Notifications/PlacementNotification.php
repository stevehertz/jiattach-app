<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlacementNotification extends Notification
{
    use Queueable;

     /**
     * The placement instance.
     */
    protected $placement;

    /**
     * The notification type.
     */
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($placement, $type = 'update')
    {
        //
        $this->placement = $placement;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->getSubject();
        $message = $this->getMessage();

        return (new MailMessage)
                ->subject($subject)
                ->greeting('Hello ' . $notifiable->first_name . '!')
                ->line($message)
                ->action('View Placement Details', url('/student/placement/status'))
                ->line('Thank you for using Jiattach!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
       return [
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'icon' => $this->getIcon(),
            'placement_id' => $this->placement->id,
            'placement_status' => $this->placement->status,
            'organization_name' => $this->placement->organization->name ?? null,
            'url' => '/student/placement/status',
            'type' => 'placement',
        ];
    }

    /**
     * Get the notification title.
     */
    protected function getTitle(): string
    {
        $titles = [
            'processing' => 'Placement Processing Started',
            'placed' => 'Placement Confirmed!',
            'admin_assigned' => 'Admin Assigned to Your Placement',
            'review' => 'Profile Under Review',
            'complete' => 'Placement Completed',
        ];

        return $titles[$this->type] ?? 'Placement Update';
    }

    /**
     * Get the notification message.
     */
    protected function getMessage(): string
    {
        $messages = [
            'processing' => 'Our team has started processing your placement request. We\'ll notify you once we find a suitable match.',
            'placed' => 'Congratulations! You have been placed at ' . ($this->placement->organization->name ?? 'an organization') . '.',
            'admin_assigned' => 'A placement coordinator has been assigned to help with your placement.',
            'review' => 'Your profile is being reviewed by our placement team.',
            'complete' => 'Your placement has been successfully completed. Well done!',
        ];

        return $messages[$this->type] ?? 'There is an update regarding your placement.';
    }

    /**
     * Get the notification subject for email.
     */
    protected function getSubject(): string
    {
        return 'Jiattach Placement Update: ' . $this->getTitle();
    }

    /**
     * Get the notification icon.
     */
    protected function getIcon(): string
    {
        $icons = [
            'processing' => 'fas fa-cogs',
            'placed' => 'fas fa-briefcase',
            'admin_assigned' => 'fas fa-user-tie',
            'review' => 'fas fa-file-alt',
            'complete' => 'fas fa-check-circle',
        ];

        return $icons[$this->type] ?? 'fas fa-bell';
    }
}
