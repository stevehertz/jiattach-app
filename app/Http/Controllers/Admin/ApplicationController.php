<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;

class ApplicationController extends Controller
{
    //
    public function show(Application $application)
    {
        return view('admin.applications.show', [
            'application' => $application
        ]);
    }
}
