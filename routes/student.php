<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\DocumentsController;
use App\Http\Controllers\Student\PlacementController;
use App\Http\Controllers\Student\CvTemplateController;
use App\Http\Controllers\Student\Dashboard\ActivityController;
use App\Http\Controllers\Student\Dashboard\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'redirect.by.role'
])->prefix('student/')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {

        Route::get('/', [ProfileController::class, 'show'])->name('show');

        Route::get('/create', [ProfileController::class, 'create'])->name('create');

        Route::post('/store', [ProfileController::class, 'store'])->name('store');

        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');

        Route::put('/update', [ProfileController::class, 'update'])->name('update');
    });

    Route::prefix('placement')->name('placement.')->group(function () {

        // Placement Routes
        Route::get('/status', [PlacementController::class, 'status'])->name('status');

        Route::get('/timeline', [PlacementController::class, 'timeline'])->name('timeline');

        Route::post('/request', [PlacementController::class, 'request'])->name('request');

    });


    // Activity & Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

    Route::get('/activity', [ActivityController::class, 'index'])->name('activity');

    // Documents
    Route::get('/documents', [DocumentsController::class, 'index'])->name('documents.index');

    // CV Templates
    Route::prefix('/cv/templates')->name('cv.templates.')->group(function () {

        Route::get('/', [CvTemplateController::class, 'index'])->name('index');

        Route::get('/download/{id}', [CvTemplateController::class, 'download'])->name('download');

    });
    



    // Mentorship routes
    Route::prefix('mentorship')->name('mentorship.')->group(function () {
        Route::get('/', function () {
            return view('students.mentorship.index');
        })->name('index');

        Route::get('/find', function () {
            return view('student.mentorship.find');
        })->name('find');

        Route::get('/sessions', function () {
            return view('students.mentorship.sessions');
        })->name('sessions');
    });
});
