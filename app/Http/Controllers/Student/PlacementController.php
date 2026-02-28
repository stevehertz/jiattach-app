<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Placement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlacementController extends Controller
{
    //
    /**
     * Display the current placement status and details.
     */
    public function status()
    {
        $user = User::findOrFail(Auth::user()->id);

        // 1. Check if officially Placed
        $placement = $user->placements()
            ->with('organization')
            ->latest()
            ->first();

        // 2. If not placed, check for applications at different stages
        $application = null;
        $pendingMatch = null;
        if (!$placement) {
            // Get the latest application
            $application = Application::where('user_id', $user->id)
                ->with(['opportunity.organization', 'opportunity'])
                ->latest()
                ->first();
            // Determine which state we're in based on application status
            if ($application) {
                if (in_array($application->status, ['pending', 'offered', 'shortlisted'])) {
                    // Match found, waiting for student action
                    $pendingMatch = $application;
                } elseif ($application->status === 'accepted') {
                    // Student has accepted, waiting for admin processing
                    // $pendingMatch remains null, $application is set
                } elseif ($application->status === 'reviewing') {
                    // Under review by admin/employer
                    // $pendingMatch remains null, $application is set
                }
            }
        }

        return view('student.placement.status', compact('placement', 'pendingMatch', 'application'));
    }

    /**
     * Accept a pending match/application.
     */
    public function acceptMatch(Request $request, $applicationId)
    {
        $user = Auth::user();
        $application = Application::where('user_id', $user->id)
            ->where('id', $applicationId)
            ->whereIn('status', ['pending', 'offered', 'shortlisted'])
            ->with('opportunity.organization')
            ->firstOrFail();

        DB::transaction(function () use ($application, $user) {
            // Update application status
            $application->status = 'accepted';
            $application->accepted_at = now();
            $application->save();

            // Log activity using the global helper function
            activity_log(
                'Student accepted the match',
                'application_accepted',
                [
                    'application_id' => $application->id,
                    'opportunity' => $application->opportunity->title,
                    'organization' => $application->opportunity->organization->name,
                    'student_name' => $user->full_name,
                    'student_id' => $user->id
                ],
                'application' // log name
            );

            // Optional: Create a placement record (if you want it created immediately)
            // Or you might wait for admin confirmation based on your workflow
            if (config('placements.auto_create_on_accept', false)) {
                $placement = Placement::create([
                    'application_id' => $application->id,
                    'student_id' => $user->id,
                    'organization_id' => $application->opportunity->organization_id,
                    'attachment_opportunity_id' => $application->attachment_opportunity_id,
                    'status' => 'pending', // or 'processing'
                    'start_date' => $application->opportunity->start_date,
                    'end_date' => $application->opportunity->end_date,
                    'placement_confirmed_at' => now(),
                ]);


                // Log activity using the global helper function
                activity_log(
                    'Placement record created from accepted application',
                    'placement_created',
                    [
                        'application_id' => $application->id,
                        'opportunity' => $application->opportunity->title,
                        'organization' => $application->opportunity->organization->name,
                        'student_name' => $user->full_name,
                        'student_id' => $user->id
                    ],
                    'application' // log name
                );




                $placement->logModelActivity(
                    '',
                    'created',
                    ['application_id' => $application->id]
                );
            }

            // Send notification to admin/employer
            // You can implement this notification later
            // $application->opportunity->organization->user->notify(new MatchAccepted($application));
        });

        return redirect()->route('student.placement.status')
            ->with('success', 'Congratulations! You have successfully accepted the match. An administrator will contact you shortly with next steps.');
    }

    /**
     * Decline a pending match/application.
     */
    public function declineMatch(Request $request, $applicationId)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $application = Application::where('user_id', $user->id)
            ->where('id', $applicationId)
            ->whereIn('status', ['pending', 'offered', 'shortlisted'])
            ->with('opportunity.organization')
            ->firstOrFail();

        DB::transaction(function () use ($application, $request) {
            // Update application status
            $application->status = 'declined_by_student';
            $application->declined_at = now();
            $application->decline_reason = $request->reason;
            $application->decline_feedback = $request->feedback;
            $application->save();

            // Log activity using your trait
            $application->logModelActivity(
                'Student declined the match',
                'declined',
                [
                    'application_id' => $application->id,
                    'reason' => $request->reason,
                    'feedback' => $request->feedback
                ]
            );

            // Send notification to admin
            // $application->opportunity->organization->user->notify(new MatchDeclined($application, $request->reason));
        });

        return redirect()->route('student.placement.status')
            ->with('info', 'You have declined this match. The system will continue searching for other opportunities that better suit your preferences.');
    }

    /**
     * Display the placement journey timeline.
     */
    public function timeline()
    {
        $user = User::findOrFail(Auth::user()->id);
        $profile = $user->studentProfile;

        // 1. Registration (Always Done)
        $timeline = [
            'registration' => [
                'status' => 'completed',
                'date' => $user->created_at,
                'title' => 'Registration',
                'description' => 'You joined the platform.'
            ]
        ];

        // 2. Profile Completion
        $isProfileComplete = $profile && $profile->profile_completeness >= 80;
        $timeline['profile'] = [
            'status' => $isProfileComplete ? 'completed' : 'in_progress',
            'date' => $profile->updated_at,
            'title' => 'Profile Completion',
            'description' => $isProfileComplete
                ? 'Profile is ready for matching.'
                : 'Complete your profile to enable auto-matching.'
        ];

        // 3. System Matching (The Core Engine)
        // This is always "In Progress" unless placed, or "Completed" if a match is found
        $match = Application::where('user_id', $user->id)->latest()->first();
        $placement = $user->placements()->latest()->first();

        $matchingStatus = 'pending'; // Default (Waiting)
        if ($placement) {
            $matchingStatus = 'completed';
        } elseif ($match) {
            $matchingStatus = 'action_required'; // Match found, waiting for student
        } elseif ($isProfileComplete) {
            $matchingStatus = 'in_progress'; // System is searching
        }

        $timeline['matching'] = [
            'status' => $matchingStatus,
            'date' => $match ? $match->created_at : null,
            'title' => 'System Matching',
            'description' => match ($matchingStatus) {
                'completed' => 'Match successfully confirmed.',
                'action_required' => 'Match found! Waiting for your acceptance.',
                'in_progress' => 'AI Algorithm is scanning opportunities for you...',
                default => 'Waiting for profile completion.'
            }
        ];

        // 4. Placement (The Goal)
        $timeline['placement'] = [
            'status' => $placement ? 'completed' : 'pending',
            'date' => $placement ? $placement->start_date : null,
            'title' => 'Placement Confirmed',
            'description' => $placement
                ? "Placed at {$placement->organization->name}"
                : 'Final placement pending.'
        ];

        return view('student.placement.timeline', compact('timeline'));
    }


    /**
     * Handle a request for placement (e.g., "I need urgent placement").
     */
    public function request(Request $request)
    {
        // Logic to trigger a priority re-scan
        $user = Auth::user();

        // Add validation to prevent spam
        $lastRequest = $user->studentProfile->last_urgent_request_at;
        if ($lastRequest && $lastRequest->diffInHours(now()) < 24) {
            return redirect()->back()
                ->with('error', 'You can only request urgent placement once every 24 hours.');
        }

        if ($user->studentProfile) {
            $user->studentProfile->update(['attachment_status' => 'urgent']);
            // Log the urgent request
            activity_log(
                'Student requested urgent placement',
                'urgent_request',
                ['user_id' => $user->id],
                'student'
            );
        }

        return redirect()->back()
            ->with('success', 'Urgency flagged. The system will prioritize your profile in the next matching cycle.');
    }
}
