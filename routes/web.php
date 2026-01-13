<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landing\PagesController;

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

Route::get('/', [PagesController::class, 'index'])->name('home');

Route::get('/students', [PagesController::class, 'students'])->name('students');

Route::get('/entrepreneurs', [PagesController::class, 'entrepreneurs'])->name('entrepreneurs');

Route::get('/mentorship', [PagesController::class, 'mentorship'])->name('mentorship');

Route::get('/about', [PagesController::class, 'about'])->name('about');

Route::get('/contact', [PagesController::class, 'contact'])->name('contact');
