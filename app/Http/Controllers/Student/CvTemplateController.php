<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CvTemplateController extends Controller
{
    //
     /**
     * List available templates.
     */
    public function index()
    {
        // In a real app, these might come from a database.
        $templates = [
            [
                'id' => 'modern-clean',
                'name' => 'Modern Professional',
                'category' => 'Corporate',
                'description' => 'A clean, two-column layout perfect for business, finance, and admin roles. Highlights experience.',
                'color' => 'primary',
                'icon' => 'fa-tie',
                'preview_image' => 'https://marketplace.canva.com/EAFRuCp3DcY/1/0/1131w/canva-black-white-minimalist-cv-resume-f5JNR-K5jjw.jpg' // Placeholder
            ],
            [
                'id' => 'technical-skill',
                'name' => 'Technical Specialist',
                'category' => 'Technology',
                'description' => 'Focuses on skills, projects, and languages. Ideal for CS, Engineering, and IT students.',
                'color' => 'info',
                'icon' => 'fa-laptop-code',
                'preview_image' => 'https://d1csarkz8obe9u.cloudfront.net/posterpreviews/software-engineer-resume-design-template-33230d4a9745582f3c7069df9e30a59a_screen.jpg' // Placeholder
            ],
            [
                'id' => 'creative-bold',
                'name' => 'Creative & Bold',
                'category' => 'Creative',
                'description' => 'Stand out with color and unique typography. Great for Media, Design, and Marketing.',
                'color' => 'warning',
                'icon' => 'fa-paint-brush',
                'preview_image' => 'https://cdn.enhancv.com/predefined-examples/marketing-manager-resume-example-1.png' // Placeholder
            ],
            [
                'id' => 'academic-simple',
                'name' => 'Standard Academic',
                'category' => 'General',
                'description' => 'Traditional format approved by most institutions. Good for research, education, and general attachment.',
                'color' => 'secondary',
                'icon' => 'fa-graduation-cap',
                'preview_image' => 'https://cdn-images.zety.com/templates/zety/valera/1024/classic-blue-valera-template.png' // Placeholder
            ],
        ];

        return view('student.documents.templates', compact('templates'));
    }

    /**
     * Download the specific template.
     */
    public function download($id)
    {
        // Define the path to your templates folder in storage
        // Ensure you put actual .docx files in storage/app/public/templates/
        $filename = $id . '.docx';
        $path = 'public/templates/' . $filename;

        // For this demo, since files don't actually exist, we'll simulate a check
        if (!Storage::exists($path)) {
            // In production, remove this mock download and ensure files exist
            return redirect()->back()->with('error', "Template file ({$filename}) not found on server. Please contact admin.");
            
            // Real code would be:
            // return Storage::download($path, "Jiattach_{$filename}");
        }

        return Storage::download($path, "Jiattach_Template_{$filename}");
    }
}
