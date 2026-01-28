<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
        
        // 2. If not placed, check if the system has found a MATCH (Stored in applications table)
        // We look for status 'pending' (waiting for student) or 'offered'
        $pendingMatch = null;
        if (!$placement) {
            $pendingMatch = Application::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'offered', 'shortlisted']) 
                ->with('opportunity.organization')
                ->latest()
                ->first();
        }

        return view('student.placement.status', compact('placement', 'pendingMatch'));
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
            'description' => match($matchingStatus) {
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
        if ($user->studentProfile) {
            $user->studentProfile->update(['attachment_status' => 'urgent']);
        }

        return redirect()->back()
            ->with('success', 'Urgency flagged. The system will prioritize your profile in the next matching cycle.');
    }
}
