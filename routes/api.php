<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FoodController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;

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
    Route::get('/foods/{id}', [FoodController::class, 'show']);
    Route::put('/foods/{id}', [FoodController::class, 'update']);
    Route::patch('/foods/{id}', [FoodController::class, 'update']);
    Route::delete('/foods/{id}', [FoodController::class, 'destroy']);

    Route::ApiResource('categories', CategoryController::class);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::get('/kitchen/orders', [OrderController::class, 'kitchenOrders']);
    Route::get('/kitchen/orders/ready', [OrderController::class, 'readyOrders']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);
    Route::patch('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    Route::get('admin/analytics', [OrderController::class, 'analytics']);

    Route::post('/food/{food}/rating', [FoodController::class, 'store']);
    Route::get('/food/{food}/rating', [FoodController::class, 'index']);
    Route::delete('/food/{food}/rating', [FoodController::class, 'destroy']);
});

