<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use App\Models\Mentor;
use App\Models\Mentorship;
use Illuminate\Http\Request;
use App\Models\MentorshipSession;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MentorshipController extends Controller
{
     /**
     * "My Mentors" - List active/past mentorships
     */
    public function index()
    {
        $user = User::findOrFail(Auth::user()->id);

        $mentorships = Mentorship::where('mentee_id', $user->id)
            ->with(['mentor.user'])
            ->orderByRaw("CASE WHEN status = 'active' THEN 1 ELSE 2 END") // Active first
            ->latest()
            ->get();

        return view('student.mentorship.index', compact('mentorships'));
    }

    /**
     * "Find a Mentor" - Browse available mentors
     */
    public function find(Request $request)
    {
        $query = Mentor::where('is_verified', true)
            ->where('availability', '!=', 'unavailable')
            ->with('user');

        // Simple Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Expertise
        if ($request->has('expertise')) {
            $query->whereJsonContains('areas_of_expertise', $request->get('expertise'));
        }

        $mentors = $query->paginate(12);

        // Get unique expertise for filter dropdown
        $allExpertise = Mentor::pluck('areas_of_expertise')->flatten()->unique()->values();

        return view('student.mentorship.find', compact('mentors', 'allExpertise'));
    }

     /**
     * "Upcoming Sessions" - Calendar/List of meetings
     */
    public function sessions()
    {
        $user = User::findOrFail(Auth::user()->id);

        // Get sessions where the user is the mentee in the parent mentorship
        $sessions = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentee_id', $user->id);
            })
            ->where('status', '!=', 'cancelled')
            ->where('scheduled_start_time', '>=', now()) // Only future
            ->with(['mentorship.mentor.user'])
            ->orderBy('scheduled_start_time', 'asc')
            ->get();

        return view('student.mentorship.sessions', compact('sessions'));
    }

    /**
     * Request a Mentorship (Action)
     */
    public function request(Request $request, $mentorId)
    {
        $user = Auth::user();

        // Check if already has active mentorship with this mentor
        $exists = Mentorship::where('mentee_id', $user->id)
            ->where('mentor_id', $mentorId)
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if($exists) {
            return back()->with('error', 'You already have a pending or active request with this mentor.');
        }

        Mentorship::create([
            'mentee_id' => $user->id,
            'mentor_id' => $mentorId,
            'status' => 'pending',
            'goals' => ['General Guidance'], // simplified for now
            'start_date' => now(),
            'requested_at' => now(),
        ]);

        return redirect()->route('student.mentorship.index')
            ->with('success', 'Mentorship request sent successfully!');
    }

}
