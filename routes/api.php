<?php

use App\Http\Controllers\Api\ReactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Reactions
Route::post('/projects/{project}/reactions', [ReactionController::class, 'store']);
Route::get('/projects/{project}/reactions', [ReactionController::class, 'getReactions']);
