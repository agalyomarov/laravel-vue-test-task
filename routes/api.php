<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/users', [UserController::class, 'index']);

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('{user}', [UserController::class, 'show']);
    Route::get('{user}/projects', [UserController::class, 'projects']);
});

Route::prefix('projects')->group(function () {
    Route::get('{project}', [ProjectController::class, 'show']);
    Route::post('/', [ProjectController::class, 'store']);
    Route::delete('{project}', [ProjectController::class, 'destroy']);

    Route::post('{project}/tasks', [TaskController::class, 'store']);
    Route::delete('{project}/tasks/{task}', [TaskController::class, 'destroy'])->scopeBindings();
});