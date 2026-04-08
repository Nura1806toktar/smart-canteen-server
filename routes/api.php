<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FoodController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('role');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/foods', [FoodController::class, 'index']);
    Route::post('/foods', [FoodController::class, 'store']);
    Route::get('/foods/{food}', [FoodController::class, 'show']);
    Route::put('/foods/{id}', [FoodController::class, 'update']);
    Route::patch('/foods/{id}', [FoodController::class, 'update']);
    Route::delete('/foods/{id}', [FoodController::class, 'destroy']);

    Route::ApiResource('categories', CategoryController::class);
});
