<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = User::findOrFail(Auth::user()->id);
        $studentProfile = $user->studentProfile;

        // Get latest placement with organization and admin eager loaded
        $placement = $user->placements()
            ->with(['organization', 'admin'])
            ->latest()
            ->first();

        // Get recent activity
        $recentActivity = \App\Models\ActivityLog::where('causer_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        // Get placement notifications
        $placementNotifications = $user->unreadNotifications()
            ->where('type', 'App\Notifications\PlacementNotification')
            ->count();

        // Get profile completeness
        $profileCompleteness = $studentProfile ? $studentProfile->profile_completeness : 0;

        return view('student_dashboard', compact(
            'user',
            'studentProfile',
            'placement',
            'recentActivity',
            'placementNotifications',
            'profileCompleteness'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
