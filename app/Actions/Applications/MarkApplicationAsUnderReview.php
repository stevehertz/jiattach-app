<?php

namespace App\Actions\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Notifications\ApplicationStatusChanged;

class MarkApplicationAsUnderReview
{
    public function execute(Application $application, ?string $notes = null): Application
    {
        // Validate transition
        if (!$application->canTransitionTo(ApplicationStatus::UNDER_REVIEW)) {
            throw new \InvalidArgumentException(
                "Cannot mark application #{$application->id} as under review from status: {$application->status->label()}"
            );
        }

        // Update status and timestamps
        $application->status = ApplicationStatus::UNDER_REVIEW;
        $application->reviewed_at = now();

        if ($notes) {
            $application->employer_notes = $notes;
        }

        $application->save();

        // Log the activity
        activity_log(
            'application_status_changed',
            "Application #{$application->id} marked as under review",
            [
                'application_id' => $application->id,
                'new_status' => $application->status->value,
                'employer_notes' => $notes
            ]
        );
            

        // Dispatch event for notifications
        event(new ApplicationStatusChanged($application, ApplicationStatus::UNDER_REVIEW->value));

        return $application->fresh();
    }

    public function executeBulk(array $applicationIds, ?string $notes = null): array
    {
        $results = [
            'success' => [],
            'failed' => []
        ];

        foreach ($applicationIds as $id) {
            try {
                $application = Application::findOrFail($id);
                $this->execute($application, $notes);
                $results['success'][] = $id;
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'id' => $id,
                    'reason' => $e->getMessage()
                ];
            }
        }

        return $results;
    }
}
