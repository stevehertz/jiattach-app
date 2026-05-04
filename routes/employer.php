<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'redirect.by.role',
])->prefix('employer')->name('employer.')->group(function () {

    Route::view('dashboard', 'employer_dashboard')->name('dashboard');

    Route::prefix('organization')->name('organization.')->group(function () {

        Route::view('/profile', 'employer.organization.profile')->name('profile');

        Route::view('/edit', 'employer.organization.edit')->name('edit');

        Route::view('/members', 'employer.organization.members')->name('members');
    });

    Route::prefix('opportunities')->name('opportunities.')->group(function () {

        Route::view('/', '')->name('index');

        Route::view('/create', '')->name('create');
    });



    Route::prefix('applications')->name('applications.')->group(function () {

        Route::view('/', 'employer.applications.index')->name('index');

        Route::get('/{application}', function ($applicationId) {
            return view('employer.applications.show', ['applicationId' => $applicationId]);
        })->name('show');
    });

    Route::prefix('placements')->name('placements.')->group(function () {

        Route::view('/', '')->name('index');

        Route::view('/create', '')->name('create');

        Route::view('/edit', '')->name('edit');
    });

    Route::prefix('matching')->name('matching.')->group(function () {

        Route::view('/suggestions', '')->name('suggestions');
    });

    Route::prefix('students')->name('students.')->group(function () {

        Route::view('/search', '')->name('search');

        Route::view('/create', '')->name('create');

        Route::view('/edit', '')->name('edit');
    });

    Route::view('/chat', '')->name('chat');

    Route::view('/profile', '')->name('profile');

    Route::view('/settings', '')->name('settings');

    Route::prefix('reports')->name('reports.')->group(function () {

        Route::view('/analytics', '')->name('analytics');

        Route::view('/overview', '')->name('overview');

        Route::view('/export', '')->name('export');
    });
});
