<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\ReserveController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/airports', [FlightController::class, 'airports']);
    Route::post('/flights', [FlightController::class, 'flights']);
    Route::post('/reserves', [ReserveController::class, 'store']);
});

