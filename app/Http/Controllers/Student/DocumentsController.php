<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DocumentsController extends Controller
{
    //
     public function index()
    {
        $profile = Auth::user()->studentProfile;
        
        $documents = [
            [
                'name' => 'Curriculum Vitae (CV)',
                'type' => 'CV / Resume',
                'url' => $profile->cv_url ?? null,
                'icon' => 'fa-file-user',
                'color' => 'danger',
                'updated_at' => $profile->updated_at
            ],
            [
                'name' => 'Academic Transcript',
                'type' => 'Transcript',
                'url' => $profile->transcript_url ?? null,
                'icon' => 'fa-file-invoice',
                'color' => 'primary',
                'updated_at' => $profile->updated_at
            ],
            [
                'name' => 'School Attachment Letter',
                'type' => 'Official Letter',
                'url' => $profile->school_letter_url ?? null,
                'icon' => 'fa-envelope-open-text',
                'color' => 'warning',
                'updated_at' => $profile->updated_at
            ]
        ];

        return view('student.documents.index', compact('documents'));
    }
}
