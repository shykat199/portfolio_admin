<?php

use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PostEditorController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\TechItemController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\PasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::post('posts/preview', [PostEditorController::class, 'preview'])->middleware('throttle:60,1')->name('posts.preview');
    Route::post('posts/images', [PostEditorController::class, 'uploadImage'])->middleware('throttle:30,1')->name('posts.images.store');
    Route::resource('posts', PostController::class)->except('show');
    Route::resource('services', ServiceController::class)->except('show');
    Route::resource('projects', ProjectController::class)->except('show');
    Route::resource('tech', TechItemController::class)->except('show');
    Route::resource('experience', ExperienceController::class)->except('show');
    Route::resource('testimonials', TestimonialController::class)->except('show');
    Route::resource('faqs', FaqController::class)->except('show');

    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::patch('messages/{message}/unread', [MessageController::class, 'markUnread'])->name('messages.unread');
    Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    Route::get('settings/site', [SiteSettingController::class, 'edit'])->name('site-settings.edit');
    Route::put('settings/site', [SiteSettingController::class, 'update'])->name('site-settings.update');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->middleware('throttle:6,1')->name('settings.password.update');

    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
});
