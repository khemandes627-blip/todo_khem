<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TodoController::class, 'index'])->name('todos.index');
Route::post('/', [TodoController::class, 'store'])->name('todos.store');
Route::patch('/toggle/{id}', [TodoController::class, 'toggle'])->name('todos.toggle');
Route::delete('/remove/{id}', [TodoController::class, 'destroy'])->name('todos.destroy');
