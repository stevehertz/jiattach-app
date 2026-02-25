<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    //
    public function show($id)
    {
        return view('admin.applications.show', [
            'applicationId' => $id
        ]);
    }
}
