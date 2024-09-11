<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
/**
 * Routes for the TaskController
 */
Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/show', [TaskController::class, 'show'])->name('tasks.show');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

/* TaskController Routes End  */
