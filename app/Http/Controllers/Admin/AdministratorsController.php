<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdministratorsController extends Controller
{
    //
    public function index()
    {
        return view('admin.administrators.index');
    }

    public function create()
    {
        return view('admin.administrators.create');
    }

    public function show($id)
    {
        $administrator = User::with(['roles', 'permissions'])
            ->where('id', $id)
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin', 'moderator']);
            })
            ->firstOrFail();
        return view('admin.administrators.show', compact('administrator'));
    }

     /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
         $administrator = User::with(['roles', 'permissions'])
            ->where('id', $id)
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin', 'moderator']);
            })
            ->firstOrFail();
        return view('admin.administrators.edit', compact('administrator'));
    }

}
