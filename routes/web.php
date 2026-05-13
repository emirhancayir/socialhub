<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SocialAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.post');
    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Posts
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/{post}/progress', [PostController::class, 'progress'])->name('posts.progress');

    // Social Accounts
    Route::get('/auth/{platform}/redirect', [SocialAccountController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{platform}/callback', [SocialAccountController::class, 'callback'])->name('social.callback');
    Route::post('/auth/demo-connect', [SocialAccountController::class, 'demoStore'])->name('social.demo-store');
    Route::delete('/social-accounts/{account}', [SocialAccountController::class, 'destroy'])->name('social.destroy');
});
