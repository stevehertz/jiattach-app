<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;

class PagesController extends Controller
{
    //
    public function index()
    {
        return view('landing.index');
    }

    public function students()
    {
        return view('landing.students');
    }

    public function entrepreneurs()
    {
        return view('landing.entrepreneurs');
    }

    public function mentorship()
    {
        return view('landing.mentorship');
    }

    public function about()
    {
        return view('landing.about');
    }

    public function contact()
    {
        return view('landing.contact');
    }
}
