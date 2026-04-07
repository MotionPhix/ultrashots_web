<?php

use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\DownloadController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\QuoteController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get(
    '/',
    [HomeController::class, 'index']
)->name('home');

// Contact Routes
Route::prefix('get-in-touch')->group(function () {
    Route::get(
        '/',
        [ContactController::class, 'load']
    )->name('contact.load');

    Route::post(
        '/',
        [ContactController::class, 'send']
    )->name('contact.send')
        ->middleware([HandlePrecognitiveRequests::class]);
});

// Quote Routes
Route::prefix('quote')->name('quote.')->group(function () {
    Route::get(
        '/',
        [QuoteController::class, 'index']
    )->name('index');

    Route::post(
        '/',
        [QuoteController::class, 'store']
    )->name('store');
});

// Newsletter Subscription Routes
Route::prefix('subscribe')->group(function () {
    Route::post(
        '/',
        [NewsletterController::class, 'subscribe']
    )->name('subscriber.enroll');

    Route::get(
        '/{token}/{email}',
        [NewsletterController::class, 'confirm']
    )->name('subscriber.confirm');

    Route::post(
        '/unsubscribe',
        [NewsletterController::class, 'unsubscribe']
    )->name('subscriber.unsubscribe');
});

// Public Portfolio Routes
Route::prefix('projects')->name('projects.')->group(function () {
    Route::get(
        '/',
        [ProjectController::class, 'index']
    )->name('index');

    Route::get(
        '/s/{project:uuid}',
        [ProjectController::class, 'show']
    )->name('show');
});

// Public Download Routes
Route::prefix('downloads')->name('public.')->group(function () {

    Route::get(
        '/',
        [DownloadController::class, 'index']
    )->name('downloads.index');

    Route::get(
        '/request-a-fix',
        [DownloadController::class, 'fixRequest']
    )->name('fix-request');

    Route::post(
        '/upload-file-to-fix',
        [DownloadController::class, 'uploadFile']
    )->name('upload-file-to-fix');

    Route::get(
        '/download/{download:uuid}',
        [DownloadController::class, 'downloadFile']
    )->name('get-download');
});
