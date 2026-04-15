<?php

use Illuminate\Support\Facades\Route;

// User Dashboard - for regular authenticated users
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/frontend.php';
require __DIR__.'/admin.php';
