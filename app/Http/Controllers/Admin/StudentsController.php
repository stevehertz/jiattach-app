<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class StudentsController extends Controller
{
    //
    public function index()
    {
        return view('admin.students.index');
    }

    public function active()
    {
        return view('admin.students.active');
    }

    public function seeking()
    {
        return view('admin.students.seeking');
    }

     public function on_attachment()
    {
        return view('admin.students.on-attachment');
    }

     public function show($id)
    {
        return view('admin.students.show');
    }


}
