<?php

use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\TechItemController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Middleware\VerifySiteSecret;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:120,1')->name('api.')->group(function () {
    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/{slug}', [PostController::class, 'show'])->name('posts.show');

    Route::get('services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('services/{slug}', [ServiceController::class, 'show'])->name('services.show');

    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

    Route::get('tech', TechItemController::class)->name('tech.index');
    Route::get('experience', ExperienceController::class)->name('experience.index');
    Route::get('testimonials', TestimonialController::class)->name('testimonials.index');
    Route::get('faqs', FaqController::class)->name('faqs.index');
    Route::get('settings', SiteSettingController::class)->name('settings.show');
});

// Enquiries from the website's contact form. Only the website (which knows
// the shared secret) can post here.
Route::post('messages', MessageController::class)
    ->middleware([VerifySiteSecret::class, 'throttle:20,1'])
    ->name('api.messages.store');
