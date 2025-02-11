<?php

use App\Http\Controllers\TravelOrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class)->except(['index', 'show', 'update', 'destroy']);
Route::apiResource('travel-orders', TravelOrderController::class)->middleware('auth:api');