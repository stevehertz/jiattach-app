<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class OfferSent extends Notification implements ShouldQueue
{
    use Queueable;

    protected Application $application;
    protected array $offerDetails;

    /**
     * Create a new notification instance.
     */
    public function __construct(Application $application, array $offerDetails)
    {
        //
        $this->application = $application;
        $this->offerDetails = $offerDetails;
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
        $stipend = $this->offerDetails['stipend'] ?? 0;
        $startDate = isset($this->offerDetails['start_date'])
            ? \Carbon\Carbon::parse($this->offerDetails['start_date'])
            : null;
        $endDate = isset($this->offerDetails['end_date'])
            ? \Carbon\Carbon::parse($this->offerDetails['end_date'])
            : null;
        $notes = $this->offerDetails['notes'] ?? null;
        $terms = $this->offerDetails['terms'] ?? null;

        $mail = (new MailMessage)
            ->subject("🎉 Congratulations! You've Received an Offer from {$this->application->opportunity->organization->name}")
            ->greeting("Hello {$notifiable->first_name},")
            ->line(new HtmlString("<strong>🎊 Exciting news!</strong> Based on your interview performance, **{$this->application->opportunity->organization->name}** would like to offer you the attachment position of **{$this->application->opportunity->title}**!"))
            ->line(new HtmlString("<br><strong>📋 Offer Details:</strong>"));

        if ($stipend > 0) {
            $mail->line("💰 Monthly Stipend: **KES " . number_format($stipend, 2) . "**");
        } else {
            $mail->line("💰 Stipend: To be discussed");
        }

        if ($startDate && $endDate) {
            $mail->line("📅 Start Date: " . $startDate->format('l, F j, Y'))
                ->line("📅 End Date: " . $endDate->format('l, F j, Y'))
                ->line("⏱️ Duration: " . $startDate->diffInMonths($endDate) . " months");
        }

        if ($notes) {
            $mail->line(new HtmlString("<br><strong>📝 Additional Information:</strong>"))
                ->line($notes);
        }

        if ($terms) {
            $mail->line(new HtmlString("<br><strong>⚖️ Terms and Conditions:</strong>"))
                ->line(new HtmlString(nl2br(e($terms))));
        }

        $mail->line(new HtmlString("<br><strong>What happens next?</strong>"))
            ->line("You have **3 days** to respond to this offer. Please review the details carefully and choose one of the following options:")
            ->line("• **Accept** - Confirm your acceptance and proceed with placement")
            ->line("• **Decline** - Politely decline this offer")
            ->line("• **Contact Us** - If you have questions before deciding");

        return $mail
            ->action('Review and Respond to Offer', url(route('student.applications.show', $this->application->id)))
            ->line(new HtmlString("<br><strong>Note:</strong> If you accept, we'll guide you through the next steps including document submission and onboarding."))
            ->line('We\'re proud of your achievement! Best of luck with your decision.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'offer_sent',
            'application_id' => $this->application->id,
            'opportunity_id' => $this->application->attachment_opportunity_id,
            'opportunity_title' => $this->application->opportunity->title,
            'organization_name' => $this->application->opportunity->organization->name,
            'stipend' => $this->offerDetails['stipend'] ?? 0,
            'start_date' => $this->offerDetails['start_date'] ?? null,
            'end_date' => $this->offerDetails['end_date'] ?? null,
            'notes' => $this->offerDetails['notes'] ?? null,
            'terms' => $this->offerDetails['terms'] ?? null,
            'sent_by' => auth()->user()->full_name ?? 'System',
            'sent_at' => now()->toDateTimeString(),
            'response_deadline' => now()->addDays(3)->toDateTimeString(),
            'icon' => 'fas fa-handshake',
            'color' => 'success',
            'message' => "You've received an offer from {$this->application->opportunity->organization->name}!",
            'action_url' => route('student.applications.show', $this->application->id),
            'action_text' => 'View Offer',
        ];
    }
}
