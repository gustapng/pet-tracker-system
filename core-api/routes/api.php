<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckApiKey;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\Api\LocationHistoryController;

Route::group(['prefix' => 'auth'], function () {
    route::post('login', [AuthController::class, 'login']);

    route::middleware('auth:api')->group(function () {
        route::post('logout', [AuthController::class, 'logout']);
        route::post('refresh', [AuthController::class, 'refresh']);
        route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:api')->group(function () {
    Route::get('pets', [PetController::class, 'index']);
    Route::post('pets', [PetController::class, 'store']);
});

Route::get('/pets/{petId}/locations', [LocationHistoryController::class, 'getHistory'])->middleware(CheckApiKey::class);