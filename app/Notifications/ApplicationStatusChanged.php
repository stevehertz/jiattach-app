<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ApplicationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected Application $application;
    protected ?string $notes;
    protected string $oldStatus;
    protected string $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Application $application, ?string $notes = null)
    {
        //
        $this->application = $application;
        $this->notes = $notes;
        $this->oldStatus = $application->getOriginal('status');
        $this->newStatus = $application->status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database', 'mail'];
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Application Status Update: {$this->getStatusLabel($this->newStatus)}")
            ->greeting("Hello {$notifiable->first_name},")
            ->line("Your application for **{$this->application->opportunity->title}** at **{$this->application->opportunity->organization->name}** has been updated.");

        // Status-specific messages
        switch ($this->newStatus) {
            case 'shortlisted':
                $mail->line("Congratulations! You have been **shortlisted** for this position.")
                    ->line("The employer will review your application and contact you for the next steps.");
                break;

            case 'interview_scheduled':
                $interviewDetails = $this->application->interview_details;
                $mail->line("An interview has been **scheduled** for your application.")
                    ->line(new HtmlString("<strong>Interview Details:</strong>"));

                if ($interviewDetails) {
                    $mail->line("Date: " . \Carbon\Carbon::parse($interviewDetails['date'])->format('l, F j, Y'))
                        ->line("Time: " . $interviewDetails['time'])
                        ->line("Type: " . ucfirst($interviewDetails['type']));

                    if (!empty($interviewDetails['location'])) {
                        $mail->line("Location/Link: " . $interviewDetails['location']);
                    }

                    if (!empty($interviewDetails['notes'])) {
                        $mail->line("Notes: " . $interviewDetails['notes']);
                    }
                }
                break;

            case 'interview_completed':
                $mail->line("Your interview has been marked as **completed**.")
                    ->line("The employer will review your performance and get back to you soon.");
                break;

            case 'offer_sent':
                $offerDetails = $this->application->offer_details;
                $mail->line(new HtmlString("<strong>🎉 Congratulations! You have received an offer!</strong>"))
                    ->line("Please review the offer details below:");

                if ($offerDetails) {
                    $mail->line("Stipend: KES " . number_format($offerDetails['stipend'], 2))
                        ->line("Start Date: " . \Carbon\Carbon::parse($offerDetails['start_date'])->format('l, F j, Y'))
                        ->line("End Date: " . \Carbon\Carbon::parse($offerDetails['end_date'])->format('l, F j, Y'));

                    if (!empty($offerDetails['notes'])) {
                        $mail->line("Additional Notes: " . $offerDetails['notes']);
                    }

                    $mail->line(new HtmlString("<br><strong>Please log in to your account to accept or decline this offer.</strong>"));
                }
                break;

            case 'offer_accepted':
                $mail->line(new HtmlString("<strong>Great news!</strong> You have accepted the offer."))
                    ->line("The employer will contact you with further instructions before your start date.");
                break;

            case 'offer_rejected':
                $mail->line("You have declined the offer.")
                    ->line("We encourage you to keep applying for other opportunities that match your profile.");
                break;

            case 'hired':
                $placement = $this->application->placement;
                $mail->line(new HtmlString("<strong>🎊 Congratulations on your new placement!</strong>"))
                    ->line("You have been officially placed at **{$this->application->opportunity->organization->name}**.");

                if ($placement) {
                    $mail->line("Start Date: " . $placement->start_date->format('l, F j, Y'))
                        ->line("End Date: " . $placement->end_date->format('l, F j, Y'))
                        ->line("Supervisor: " . $placement->supervisor_name)
                        ->line("Department: " . $placement->department);
                }
                break;

            case 'rejected':
                $mail->line("We regret to inform you that your application has not been successful.")
                    ->line("Don't be discouraged! Many other opportunities are available that might be a better fit for your skills and experience.");
                break;

            default:
                $mail->line("Your application status has been updated to: **{$this->getStatusLabel($this->newStatus)}**");

                // Add any admin notes
                if ($this->notes) {
                    $mail->line(new HtmlString("<br><strong>Additional Notes from the Employer:</strong>"))
                        ->line($this->notes);
                }

                return $mail
                    ->action('View Application', url(route('student.applications.show', $this->application->id)))
                    ->line('Thank you for using our platform!');
        }



        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusColors = [
            'submitted' => 'info',
            'under_review' => 'primary',
            'shortlisted' => 'success',
            'interview_scheduled' => 'warning',
            'interview_completed' => 'info',
            'offer_sent' => 'success',
            'offer_accepted' => 'success',
            'offer_rejected' => 'danger',
            'hired' => 'success',
            'rejected' => 'danger',
        ];

        return [
            //
            'type' => 'application_status_change',
            'application_id' => $this->application->id,
            'opportunity_id' => $this->application->attachment_opportunity_id,
            'opportunity_title' => $this->application->opportunity->title,
            'organization_name' => $this->application->opportunity->organization->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'status_label' => $this->getStatusLabel($this->newStatus),
            'status_color' => $statusColors[$this->newStatus] ?? 'secondary',
            'notes' => $this->notes,
            'icon' => $this->getStatusIcon($this->newStatus),
            'message' => $this->getNotificationMessage(),
            'action_url' => route('student.applications.show', $this->application->id),
            'action_text' => 'View Application',
        ];
    }

      /**
     * Get the status label.
     */
    protected function getStatusLabel(string $status): string
    {
        return match($status) {
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'shortlisted' => 'Shortlisted',
            'interview_scheduled' => 'Interview Scheduled',
            'interview_completed' => 'Interview Completed',
            'offer_sent' => 'Offer Sent',
            'offer_accepted' => 'Offer Accepted',
            'offer_rejected' => 'Offer Declined',
            'hired' => 'Hired/Placed',
            'rejected' => 'Not Selected',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Get the status icon.
     */
    protected function getStatusIcon(string $status): string
    {
        return match($status) {
            'submitted' => 'fas fa-file-alt',
            'under_review' => 'fas fa-search',
            'shortlisted' => 'fas fa-list-check',
            'interview_scheduled' => 'fas fa-calendar-check',
            'interview_completed' => 'fas fa-check-circle',
            'offer_sent' => 'fas fa-handshake',
            'offer_accepted' => 'fas fa-check-double',
            'offer_rejected' => 'fas fa-times-circle',
            'hired' => 'fas fa-user-check',
            'rejected' => 'fas fa-ban',
            default => 'fas fa-circle',
        };
    }

     /**
     * Get the notification message.
     */
    protected function getNotificationMessage(): string
    {
        return match($this->newStatus) {
            'shortlisted' => 'Congratulations! You have been shortlisted.',
            'interview_scheduled' => 'Interview has been scheduled.',
            'interview_completed' => 'Interview completed. Awaiting feedback.',
            'offer_sent' => 'You have received an offer!',
            'offer_accepted' => 'Offer accepted successfully.',
            'offer_rejected' => 'Offer declined.',
            'hired' => 'Congratulations! You have been placed.',
            'rejected' => 'Application not successful.',
            default => "Status updated to: {$this->getStatusLabel($this->newStatus)}",
        };
    }

    /**
     * Determine if SMS should be sent.
     */
    protected function shouldSendSms(): bool
    {
        $importantStatuses = ['offer_sent', 'interview_scheduled', 'hired'];
        return in_array($this->newStatus, $importantStatuses);
    }
}
