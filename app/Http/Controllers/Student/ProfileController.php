<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show()
    {
        //
        $user = User::findOrFail(Auth::user()->id);
        $profile = $user->studentProfile;
        if (!$profile)
        {
            return redirect()->route('student.profile.edit')->with('info', 'Please create your profile first.');
        }

        $progressBreakdown = $profile->getProfileProgressBreakdown();
        $missingFields = $profile->getMissingFields();

        return view('student.profile.show', compact(
            'profile',
            'progressBreakdown',
            'missingFields'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
        $user = User::findOrFail(Auth::user()->id);
        $profile = $user->studentProfile();
        return view('student.profile.edit', compact('profile'));
    }

     /**
     * Handle document upload via traditional form (alternative to Livewire).
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:cv,transcript',
            'document' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $user = Auth::user();
        $profile = $user->studentProfile;

        if (!$profile) {
            return redirect()->route('student.profile.edit')
                ->with('error', 'Please create your profile first.');
        }

        // Upload document
        $path = $request->file('document')->store('documents/student/' . $user->id, 'public');
        $url = Storage::url($path);

        // Update profile
        if ($request->document_type == 'cv') {
            $profile->cv_url = $url;
        } else {
            $profile->transcript_url = $url;
        }

        $profile->save();

        // Log activity
        // activity()
        //     ->causedBy($user)
        //     ->performedOn($profile)
        //     ->log('uploaded ' . $request->document_type);

        return redirect()->route('student.profile.edit')
            ->with('success', ucfirst($request->document_type) . ' uploaded successfully!');
    }

      /**
     * Download a document.
     */
    public function downloadDocument($type)
    {
        $user = Auth::user();
        $profile = $user->studentProfile;

        if (!$profile) {
            abort(404);
        }

        $field = $type == 'cv' ? 'cv_url' : 'transcript_url';
        $url = $profile->$field;

        if (!$url) {
            abort(404, 'Document not found.');
        }

        $filePath = str_replace('/storage/', '', $url);
        $fullPath = storage_path('app/public/' . $filePath);

        if (!file_exists($fullPath)) {
            abort(404, 'File not found.');
        }

        return response()->download($fullPath);
    }

    /**
     * Get profile completeness data for AJAX requests.
     */
    public function getProfileProgress(Request $request)
    {
        $user = Auth::user();
        $profile = $user->studentProfile;

        if (!$profile) {
            return response()->json([
                'completeness' => 0,
                'is_ready' => false,
                'missing_fields' => [],
            ]);
        }

        return response()->json([
            'completeness' => $profile->profile_completeness,
            'is_ready' => $profile->isPlacementReady(),
            'missing_fields' => $profile->getMissingFields(),
            'breakdown' => $profile->getProfileProgressBreakdown(),
        ]);
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
