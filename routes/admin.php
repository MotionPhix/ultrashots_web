<?php

/*
|--------------------------------------------------------------------------
| Admin Dashboard Routes
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
  ->prefix('admin')->name('admin.')
  ->group(function () {

  // Dashboard
  Route::get(
    '/',
    [\App\Http\Controllers\Admin\DashboardController::class, 'index']
  )->name('dashboard');

  Route::post(
    '/refresh',
    [\App\Http\Controllers\Admin\DashboardController::class, 'refresh']
  )->name('dashboard.refresh');

  // Customer Management
  Route::prefix('customers')->name('customers.')->group(function () {

    Route::get(
      '/',
      [\App\Http\Controllers\Admin\CustomerController::class, 'index']
    )->name('index');

    Route::get(
      '/create',
      [\App\Http\Controllers\Admin\CustomerController::class, 'create']
    )->name('create');

    Route::post(
      '/',
      [\App\Http\Controllers\Admin\CustomerController::class, 'store']
    )->name('store');

    Route::get(
      '/s/{customer:uuid}',
      [\App\Http\Controllers\Admin\CustomerController::class, 'show']
    )->name('show');

    Route::get(
      '/e/{customer:uuid}',
      [\App\Http\Controllers\Admin\CustomerController::class, 'edit']
    )->name('edit');

    Route::patch(
      '/{customer:uuid}',
      [\App\Http\Controllers\Admin\CustomerController::class, 'update']
    )->name('update');

    Route::delete(
      '/{customer:uuid}',
      [\App\Http\Controllers\Admin\CustomerController::class, 'destroy']
    )->name('destroy');

  });

  // Project Management
  Route::prefix('projects')->name('projects.')->group(function () {
    Route::get(
      '/',
      [\App\Http\Controllers\Admin\ProjectController::class, 'listing']
    )->name('index');

    Route::get(
      '/create/{customer:uuid?}',
      [\App\Http\Controllers\Admin\ProjectController::class, 'create']
    )->name('create');

    Route::post(
      '/',
      [\App\Http\Controllers\Admin\ProjectController::class, 'store']
    )->name('store');

    Route::get(
      '/s/{project:uuid}',
      [\App\Http\Controllers\Admin\ProjectController::class, 'detail']
    )->name('show');

    Route::get(
      '/e/{project:uuid}',
      [\App\Http\Controllers\Admin\ProjectController::class, 'edit']
    )->name('edit');

    Route::put(
      '/{project:uuid}',
      [\App\Http\Controllers\Admin\ProjectController::class, 'update']
    )->name('update');

    Route::delete(
      '/{project:uuid}',
      [\App\Http\Controllers\Admin\ProjectController::class, 'destroy']
    )->name('destroy');
  });

  // Downloads Management
  Route::prefix('downloads')->name('downloads.')->group(function () {
    Route::get(
      '/list',
      [\App\Http\Controllers\Admin\DownloadController::class, 'index']
    )->name('index');

    Route::get(
      '/new',
      [\App\Http\Controllers\Admin\DownloadController::class, 'create']
    )->name('create');

    Route::get(
      '/s/{download:uuid}',
      [\App\Http\Controllers\Admin\DownloadController::class, 'show']
    )->name('show');

    Route::get(
      '/e/{download:uuid}',
      [\App\Http\Controllers\Admin\DownloadController::class, 'edit']
    )->name('edit');

    Route::put(
      '/u/{download:uuid}',
      [\App\Http\Controllers\Admin\DownloadController::class, 'update']
    )->name('update');

    Route::delete(
      '/d/{download:uuid}',
      [\App\Http\Controllers\Admin\DownloadController::class, 'destroy']
    )->name('destroy');

    // Bulk operations
    Route::post(
      '/bulk-update',
      [\App\Http\Controllers\Admin\DownloadController::class, 'bulkUpdate']
    )->name('bulk-update');

    Route::post(
      '/reorder',
      [\App\Http\Controllers\Admin\DownloadController::class, 'reorder']
    )->name('reorder');

    // Statistics
    Route::get(
      '/stats',
      [\App\Http\Controllers\Admin\DownloadController::class, 'stats']
    )->name('stats');

    // Export functionality
    Route::get(
      '/export',
      [\App\Http\Controllers\Admin\DownloadController::class, 'export']
    )->name('export');

    // Duplicate download
    Route::post(
      '/duplicate/{download:uuid}',
      [\App\Http\Controllers\Admin\DownloadController::class, 'duplicate']
    )->name('duplicate');

    // Download file endpoint
    Route::get(
      '/download/{download:uuid}',
      [\App\Http\Controllers\Admin\DownloadController::class, 'download']
    )->name('download');

    Route::post(
      '/',
      [\App\Http\Controllers\Admin\DownloadController::class, 'store']
    )->name('store');
  });

  // Profile Management
  /*Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::delete('/', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('destroy');
  });*/
});
