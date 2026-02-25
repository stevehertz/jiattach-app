<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class FeedbackReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected Application $application;
    protected Feedback $feedback;

    /**
     * Create a new notification instance.
     */
    public function __construct(Application $application, Feedback $feedback)
    {
        //
        $this->application = $application;
        $this->feedback = $feedback;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $feedbackType = $this->getFeedbackTypeLabel($this->feedback->type);
        $senderName = $this->feedback->user->full_name ?? 'The Employer';
        $senderRole = $this->feedback->user->roles->first()->name ?? 'Administrator';

        $mail = (new MailMessage)
            ->subject("Feedback Received: {$this->application->opportunity->title}")
            ->greeting("Hello {$notifiable->first_name},")
            ->line(new HtmlString("<strong>📝 You've received {$feedbackType} feedback</strong> regarding your application for **{$this->application->opportunity->title}** at **{$this->application->opportunity->organization->name}**."));

         // Type-specific intro
        switch ($this->feedback->type) {
            case 'interview':
                $mail->line("Thank you for attending the interview. Here's some feedback from the interview panel:");
                break;
            case 'offer':
                $mail->line("Regarding the offer sent to you, here's some additional information:");
                break;
            case 'rejection':
                $mail->line("While your application was not successful this time, we wanted to provide you with some constructive feedback:");
                break;
            default:
                $mail->line("Here's some feedback regarding your application:");
        }

        $mail->line(new HtmlString("<br><div style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 10px 0;'>"))
             ->line(new HtmlString("<strong>From:</strong> {$senderName} ({$senderRole})"))
             ->line(new HtmlString("<br><strong>Feedback:</strong>"))
             ->line(new HtmlString(nl2br(e($this->feedback->message))))
             ->line(new HtmlString("</div>"));

             // Add encouraging words based on feedback type
        if ($this->feedback->type === 'rejection') {
            $mail->line(new HtmlString("<br><strong>💪 Keep Going!</strong>"))
                 ->line("Rejection is not a reflection of your worth. Many successful professionals faced rejections before finding their perfect role. Use this feedback to grow and improve.");
        } elseif ($this->feedback->type === 'interview') {
            $mail->line(new HtmlString("<br><strong>🌟 Next Steps</strong>"))
                 ->line("Use this feedback to prepare for future interviews. Every interview is a learning experience!");
        } else {
            $mail->line(new HtmlString("<br><strong>🙏 Thank You</strong>"))
                 ->line("We appreciate your interest in this opportunity and encourage you to keep applying.");
        }

        return $mail
            ->action('View Full Feedback', url(route('student.applications.show', $this->application->id)))
            ->line('If you have any questions about this feedback, please don\'t hesitate to reach out to us.')
            ->line('Keep pursuing your goals! 🚀');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $feedbackColors = [
            'general' => 'info',
            'interview' => 'primary',
            'offer' => 'success',
            'rejection' => 'danger',
        ];

        $feedbackIcons = [
            'general' => 'fas fa-comment',
            'interview' => 'fas fa-calendar-alt',
            'offer' => 'fas fa-handshake',
            'rejection' => 'fas fa-times-circle',
        ];

        return [
            'type' => 'feedback_received',
            'feedback_type' => $this->feedback->type,
            'feedback_type_label' => $this->getFeedbackTypeLabel($this->feedback->type),
            'application_id' => $this->application->id,
            'opportunity_id' => $this->application->attachment_opportunity_id,
            'opportunity_title' => $this->application->opportunity->title,
            'organization_name' => $this->application->opportunity->organization->name,
            'feedback_id' => $this->feedback->id,
            'feedback_message' => $this->feedback->message,
            'feedback_preview' => substr($this->feedback->message, 0, 100) . (strlen($this->feedback->message) > 100 ? '...' : ''),
            'sender_name' => $this->feedback->user->full_name ?? 'System',
            'sender_role' => $this->feedback->user->roles->first()->name ?? 'Administrator',
            'sent_at' => $this->feedback->created_at->toDateTimeString(),
            'icon' => $feedbackIcons[$this->feedback->type] ?? 'fas fa-comment',
            'color' => $feedbackColors[$this->feedback->type] ?? 'secondary',
            'message' => "You've received {$this->getFeedbackTypeLabel($this->feedback->type)} feedback",
            'action_url' => route('student.applications.show', $this->application->id),
            'action_text' => 'Read Feedback',
        ];
    }

     /**
     * Get feedback type label.
     */
    protected function getFeedbackTypeLabel(string $type): string
    {
        return match($type) {
            'general' => 'general',
            'interview' => 'interview',
            'offer' => 'offer-related',
            'rejection' => 'rejection',
            default => $type,
        };
    }
}
