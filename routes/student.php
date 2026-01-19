<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\Dashboard\ActivityController;
use App\Http\Controllers\Student\Dashboard\DashboardController;
use App\Http\Controllers\Student\Dashboard\PlacementController;
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
    'redirect.by.role',
])->prefix('student/')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Placement Routes
    Route::get('/placement/status', [PlacementController::class, 'status'])
        ->name('placement.status');

    Route::get('/placement/timeline', [PlacementController::class, 'timeline'])
        ->name('placement.timeline');

    Route::post('/placement/request', [PlacementController::class, 'request'])
        ->name('placement.request');

    // Activity & Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications');

    Route::get('/activity', [ActivityController::class, 'index'])
        ->name('activity');

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
