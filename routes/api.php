<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterController::class)->middleware('throttle:register');

Route::post('/login', LoginController::class)->middleware('throttle:login');

Route::middleware('auth:sanctum', 'throttle:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/todos', [TodoController::class, 'index']);
    Route::post('/todos', [TodoController::class, 'store']);
    Route::patch('/todos/{todo}', [TodoController::class, 'update'])
        ->missing(function () {
            return response()->json(['message' => 'Todo not found.'], 404);
        });
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])
        ->name('todos.destroy')
        ->missing(function () {
            return response()->json(['message' => 'Todo not found.'], 404);
        });
});
