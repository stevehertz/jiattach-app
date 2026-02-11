<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\AttachmentOpportunity;

class OpportunityController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.opportunities.index');
    }

    public function create()
    {
        return view('admin.opportunities.create');
    }

    public function show(AttachmentOpportunity $opportunity): View
    {
        return view('admin.opportunities.show', [
            'opportunity' => $opportunity->load([
                'organization',
                'applications.student.studentProfile',
                'applications' => function ($query) {
                    $query->latest()->take(20);
                }
            ])
        ]);
    }

    public function edit()
    {
        return view('admin.opportunities.edit');
    }
}
