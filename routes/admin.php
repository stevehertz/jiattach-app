<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Students\Active;
use App\Livewire\Admin\Students\Seeking;
use App\Livewire\Admin\Students\OnAttachment;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\StudentsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MentorshipController;
use App\Http\Controllers\Admin\OpportunityController;
use App\Http\Controllers\Admin\AdministratorsController;
use App\Livewire\Admin\Opportunities\Active as OpportunitiesActive;
use App\Livewire\Admin\Opportunities\Pending;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded  by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'redirect.by.role',
])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class,  'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    Route::prefix('users')->name('users.')->group(function () {

        Route::get('/', [UsersController::class, 'index'])->name('index');

        Route::get('/{user}/users', [UsersController::class, 'show'])->name('show');
    });

    // Add this to your admin routes group, after the mentors routes
    Route::prefix('administrators')->name('administrators.')->group(function () {

        Route::get('/', [AdministratorsController::class, 'index'])->name('index');

        Route::get('/create', [AdministratorsController::class, 'create'])->name('create');

        Route::get('/{administrator}', [AdministratorsController::class, 'show'])->name('show');

        Route::get('/{administrator}/edit', [AdministratorsController::class, 'edit'])->name('edit');

        Route::get('/super-admins', [AdministratorsController::class, 'super-admins'])->name('super.admins');
    });

    // Add to your existing admin routes group
    Route::prefix('students')->name('students.')->group(function () {

        Route::get('/', [StudentsController::class, 'index'])->name('index');

        Route::get('/active', Active::class)->name('active');

        Route::get('/seeking', Seeking::class)->name('seeking');

        Route::get('/on-attachment', OnAttachment::class)->name('on-attachment');

        // Individual student view
        Route::get('/{student}', [StudentsController::class, 'show'])->name('show');
    });

    // Opportunities Management Routes
    Route::prefix('opportunities')->name('opportunities.')->group(function () {

        // All Opportunities
        Route::get('/', [OpportunityController::class, 'index'])->name('index');

        // Active Opportunities
        Route::get('/active', OpportunitiesActive::class)->name('active');

        // Pending Approval Opportunities
        Route::get('/pending', Pending::class)->name('pending');

        // Create New Opportunity
        Route::get('/create', [OpportunityController::class, 'create'])->name('create');

        // View Single Opportunity
        Route::get('/{opportunity}/show', [OpportunityController::class, 'show'])->name('show');

        // Edit Opportunity
        Route::get('/{opportunity}/edit', [OpportunityController::class, 'edit'])->name('edit');
    });

    // Mentorship Management Routes
    Route::prefix('mentorships')->name('mentorships.')->group(function () {
        // All Mentorships
        Route::get('/', [MentorshipController::class, 'index'])->name('index');

        // Active Mentorships
        Route::get('/active', [MentorshipController::class, 'active'])->name('active');

        // Upcoming Sessions
        Route::get('/upcoming-sessions', [MentorshipController::class, 'upcomingSessions'])->name('upcoming.sessions');

        // Completed Mentorships
        Route::get('/completed', [MentorshipController::class, 'completed'])->name('completed');

        // Reviews & Ratings
        Route::get('/reviews', [MentorshipController::class, 'reviews'])->name('reviews');

        // View Single Mentorship
        Route::get('/{mentorship}/show', [MentorshipController::class, 'show'])->name('show');

        // Edit Mentorship
        Route::get('/{mentorship}/edit', [MentorshipController::class, 'edit'])->name('edit');

        // Add New Mentorship
        Route::get('/create', [MentorshipController::class, 'create'])->name('create');
    });

    // Reports & Analytics Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        // Analytics Dashboard
        Route::view('/analytics-dashboard', 'admin.reports.analytics-dashboard')->name('analytics-dashboard');

        // Placement Reports
        Route::view('/placement-reports', 'admin.reports.placement-reports')->name('placement-reports');

        // User Statistics
        Route::view('/user-statistics', 'admin.reports.user-statistics')->name('user-statistics');

        // Opportunity Analytics
        Route::view('/opportunity-analytics', 'admin.reports.opportunity-analytics')->name('opportunity-analytics');

        // Application Reports (optional)
        Route::view('/application-reports', 'admin.reports.application-reports')->name('application-reports');

        // Financial Reports (optional)
        Route::view('/financial-reports', 'admin.reports.financial-reports')->name('financial-reports');

        // Export Reports
        Route::view('/export-reports', 'admin.reports.export-reports')->name('export-reports');
    });

    // Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {

        Route::view('/general', 'admin.settings.general')->name('general');

        Route::view('/email', 'admin.settings.email')->name('email');

        Route::view('/payment', 'admin.settings.payment')->name('payment');

        Route::view('/notifications', 'admin.settings.notifications')->name('notifications');

        Route::view('/security', 'admin.settings.security')->name('security');

        Route::view('/backup', 'admin.settings.backup')->name('backup');
    });

    Route::view('activity-logs', 'admin.activity-logs')->name('activity-logs');

    Route::view('system-health', 'admin.system-health')->name('system-health');

    Route::view('database', 'admin.database.index')->name('database');

    Route::view('help', 'admin.help')->name('help');

    Route::view('documentation', 'admin.documentation')->name('documentation');
});
