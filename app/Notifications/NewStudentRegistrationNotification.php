<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewStudentRegistrationNotification extends Notification
{
    use Queueable;

    protected $student;
    protected $studentProfile;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $student)
    {
        //
        $this->student = $student;
        $this->studentProfile = $student->studentProfile;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Start with just database to test
        $channels = ['database'];

        // Only add broadcast if Pusher is configured properly
        if (config('broadcasting.default') === 'pusher' && env('PUSHER_APP_KEY')) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Student Registration',
            'message' => "New student {$this->student->full_name} has registered",
            'type' => 'student_registration',
            'icon' => 'fas fa-user-graduate',
            'data' => [
                'student_id' => $this->student->id,
                'student_name' => $this->student->full_name,
                'student_email' => $this->student->email,
                'institution' => $this->studentProfile?->institution_name,
                'course' => $this->studentProfile?->course_name,
                'registered_at' => $this->student->created_at->toISOString(),
            ],
            'url' => route('admin.students.show', $this->student->id),
        ];
    }


    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable)
    {
        return [
            'notification' => $this->toArray($notifiable),
            'timestamp' => now(),
        ];
    }
}
