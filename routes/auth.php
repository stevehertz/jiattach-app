<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;


// Social Auth Routes
Route::get('auth/{provider}', [SocialAuthController::class, 'redirect'])
    ->where('provider', 'google|linkedin')
    ->name('social.redirect');

Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->where('provider', 'google|linkedin');