<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::get('{user:id}', [UserController::class, 'show']);
    Route::get('{user:id}/projects', [UserController::class, 'projects']);
});

Route::prefix('projects')->group(function () {
    Route::get('{project:id}', [ProjectController::class, 'show']);
    Route::post('/', [ProjectController::class, 'store']);
    Route::delete('{project:id}', [ProjectController::class, 'destroy']);

    Route::post('{project:id}/tasks', [TaskController::class, 'store']);
    Route::delete('{project:id}/tasks/{task:id}', [TaskController::class, 'destroy']);
});