<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Homepage
Route::get(
  '/',
  [\App\Http\Controllers\Frontend\HomeController::class, 'index']
)->name('home');

// Contact Routes
Route::prefix('get-in-touch')->group(function () {
  Route::get(
    '/',
    [\App\Http\Controllers\Frontend\ContactController::class, 'load']
  )->name('contact.load');

  Route::post(
    '/',
    [\App\Http\Controllers\Frontend\ContactController::class, 'send']
  )->name('contact.send');
});

// Quote Routes
Route::prefix('quote')->name('quote.')->group(function () {
  Route::get(
    '/',
    [\App\Http\Controllers\Frontend\QuoteController::class, 'index']
  )->name('index');

  Route::post(
    '/',
    [\App\Http\Controllers\Frontend\QuoteController::class, 'store']
  )->name('store');
});

// Newsletter Subscription Routes
Route::prefix('subscribe')->group(function () {
  Route::post(
    '/',
    [\App\Http\Controllers\Frontend\NewsletterController::class, 'subscribe']
  )->name('subscriber.enroll');

  Route::get(
    '/{token}/{email}',
    [\App\Http\Controllers\Frontend\NewsletterController::class, 'confirm']
  )->name('subscriber.confirm');

  Route::post(
    '/unsubscribe',
    [\App\Http\Controllers\Frontend\NewsletterController::class, 'unsubscribe']
  )->name('subscriber.unsubscribe');
});

// Public Portfolio Routes
Route::prefix('projects')->name('projects.')->group(function () {
  Route::get(
    '/',
    [\App\Http\Controllers\Frontend\ProjectController::class, 'index']
  )->name('index');

  Route::get(
    '/s/{project:uuid}',
    [\App\Http\Controllers\Frontend\ProjectController::class, 'show']
  )->name('show');
});

// Public Download Routes
Route::prefix('downloads')->name('public.')->group(function () {

  Route::get(
    '/',
    [\App\Http\Controllers\Frontend\DownloadController::class, 'index']
  )->name('downloads.index');

  Route::get(
    '/request-a-fix',
    [\App\Http\Controllers\Frontend\DownloadController::class, 'fixRequest']
  )->name('fix-request');

  Route::post(
    '/upload-file-to-fix',
    [\App\Http\Controllers\Frontend\DownloadController::class, 'uploadFile']
  )->name('upload-file-to-fix');

  Route::get(
    '/download/{download:uuid}',
    [\App\Http\Controllers\Frontend\DownloadController::class, 'downloadFile']
  )->name('get-download');
});

require __DIR__.'/settings.php';
require __DIR__.'/frontend.php';
require __DIR__.'/admin.php';
