<?php

namespace App\Traits;

trait TracksApplicationHistory
{
    /**
     * Boot the trait.
     */
    protected static function bootTracksApplicationHistory()
    {
        // Track when application is created
        static::created(function ($application) {
            $application->addHistory(
                'created',
                $application->student_id,
                $application->organization_id,
                null,
                $application->status->value ?? 'pending',
                'Application submitted',
                [
                    'match_score' => $application->match_score,
                    'opportunity_id' => $application->attachment_opportunity_id,
                ]
            );
        });

        // Track when application status is updated
        static::updated(function ($application) {
            if ($application->isDirty('status')) {
                $oldStatus = $application->getOriginal('status');
                $newStatus = $application->status;

                // Don't log if it's the same status
                if ($oldStatus == $newStatus) {
                    return;
                }

                $action = 'status_changed';

                // Determine specific action based on new status
                if ($newStatus?->value === 'interview_scheduled') {
                    $action = 'interview_scheduled';
                } elseif ($newStatus?->value === 'interview_completed') {
                    $action = 'interview_completed';
                } elseif ($newStatus?->value === 'offer_sent') {
                    $action = 'offer_sent';
                } elseif ($newStatus?->value === 'offer_accepted') {
                    $action = 'offer_accepted';
                } elseif ($newStatus?->value === 'offer_rejected') {
                    $action = 'offer_rejected';
                } elseif ($newStatus?->value === 'hired') {
                    $action = 'hired';
                } elseif ($newStatus?->value === 'rejected') {
                    $action = 'rejected';
                }

                $metadata = [];

                // Add interview details if available
                if ($application->isDirty('interview_details') && $application->interview_details) {
                    $metadata['interview_details'] = $application->interview_details;
                }

                // Add offer details if available
                if ($application->isDirty('offer_details') && $application->offer_details) {
                    $metadata['offer_details'] = $application->offer_details;
                }

                $application->addHistory(
                    $action,
                    $oldStatus instanceof \App\Enums\ApplicationStatus ? $oldStatus->value : $oldStatus,
                    $newStatus instanceof \App\Enums\ApplicationStatus ? $newStatus->value : $newStatus,
                    "Status changed from " .
                        ($oldStatus instanceof \App\Enums\ApplicationStatus ? $oldStatus->label() : $oldStatus) .
                        " to " .
                        ($newStatus instanceof \App\Enums\ApplicationStatus ? $newStatus->label() : $newStatus),
                    $metadata
                );
            }
        });
    }
}
