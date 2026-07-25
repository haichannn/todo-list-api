<?php

use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterController::class)->middleware('throttle:register');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
