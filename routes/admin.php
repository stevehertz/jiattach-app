<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\DashboardController;

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
});
