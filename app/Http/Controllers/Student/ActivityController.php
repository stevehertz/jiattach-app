<?php

namespace App\Http\Controllers\Student;

use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    //
    public function index()
    {
        // Fetch logs caused by the current user
        $activities = ActivityLog::where('causer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Group activities by date for the timeline view
        $groupedActivities = $activities->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });

        return view('student.activity.index', compact('groupedActivities', 'activities'));
    }
}
