<?php

/*
|--------------------------------------------------------------------------
| Admin Dashboard Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'is_admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get(
            '/',
            [DashboardController::class, 'index']
        )->name('dashboard');

        Route::post(
            '/refresh',
            [DashboardController::class, 'refresh']
        )->name('dashboard.refresh');

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get(
                '/',
                [NotificationController::class, 'index']
            )->name('index');

            Route::patch(
                '/{notification}/read',
                [NotificationController::class, 'markAsRead']
            )->name('mark-read');

            Route::patch(
                '/read-all',
                [NotificationController::class, 'markAllAsRead']
            )->name('mark-all-read');

            Route::delete(
                '/{notification}',
                [NotificationController::class, 'delete']
            )->name('delete');

            Route::get(
                '/unread-count',
                [NotificationController::class, 'getUnreadCount']
            )->name('unread-count');
        });

        // Customer Management
        Route::prefix('customers')->name('customers.')->group(function () {

            Route::get(
                '/',
                [CustomerController::class, 'index']
            )->name('index');

            Route::get(
                '/create',
                [CustomerController::class, 'create']
            )->name('create');

            Route::post(
                '/',
                [CustomerController::class, 'store']
            )->name('store');

            Route::get(
                '/s/{customer:uuid}',
                [CustomerController::class, 'show']
            )->name('show');

            Route::get(
                '/e/{customer:uuid}',
                [CustomerController::class, 'edit']
            )->name('edit');

            Route::patch(
                '/{customer:uuid}',
                [CustomerController::class, 'update']
            )->name('update');

            Route::delete(
                '/{customer:uuid}',
                [CustomerController::class, 'destroy']
            )->name('destroy');

        });

        // Project Management
        Route::prefix('projects')->name('projects.')->group(function () {
            Route::get(
                '/',
                [ProjectController::class, 'listing']
            )->name('index');

            Route::get(
                '/create/{customer:uuid?}',
                [ProjectController::class, 'create']
            )->name('create');

            Route::post(
                '/',
                [ProjectController::class, 'store']
            )->name('store');

            Route::get(
                '/s/{project:uuid}',
                [ProjectController::class, 'detail']
            )->name('show');

            Route::get(
                '/e/{project:uuid}',
                [ProjectController::class, 'edit']
            )->name('edit');

            Route::put(
                '/{project:uuid}',
                [ProjectController::class, 'update']
            )->name('update');

            Route::delete(
                '/{project:uuid}',
                [ProjectController::class, 'destroy']
            )->name('destroy');
        });

        // Downloads Management
        Route::prefix('downloads')->name('downloads.')->group(function () {
            Route::get(
                '/list',
                [DownloadController::class, 'index']
            )->name('index');

            Route::get(
                '/new',
                [DownloadController::class, 'create']
            )->name('create');

            Route::get(
                '/s/{download:uuid}',
                [DownloadController::class, 'show']
            )->name('show');

            Route::get(
                '/e/{download:uuid}',
                [DownloadController::class, 'edit']
            )->name('edit');

            Route::put(
                '/u/{download:uuid}',
                [DownloadController::class, 'update']
            )->name('update');

            Route::delete(
                '/d/{download:uuid}',
                [DownloadController::class, 'destroy']
            )->name('destroy');

            // Bulk operations
            Route::post(
                '/bulk-update',
                [DownloadController::class, 'bulkUpdate']
            )->name('bulk-update');

            Route::post(
                '/reorder',
                [DownloadController::class, 'reorder']
            )->name('reorder');

            // Statistics
            Route::get(
                '/stats',
                [DownloadController::class, 'stats']
            )->name('stats');

            // Export functionality
            Route::get(
                '/export',
                [DownloadController::class, 'export']
            )->name('export');

            // Duplicate download
            Route::post(
                '/duplicate/{download:uuid}',
                [DownloadController::class, 'duplicate']
            )->name('duplicate');

            // Download file endpoint
            Route::get(
                '/download/{download:uuid}',
                [DownloadController::class, 'download']
            )->name('download');

            Route::post(
                '/',
                [DownloadController::class, 'store']
            )->name('store');
        });
    });
