<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CalificationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::apiResource('products.califications', CalificationController::class)->only('index');
Route::apiResource('sections', SectionController::class)->only('index');
Route::apiResource('categories', CategoryController::class)->only('index');
Route::apiResource('products', ProductController::class)->only('index', 'show');
Route::apiResource('stocks', StockController::class)->only('index');
Route::apiResource('images', ImageController::class)->only('index');

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(
    function()
    {
        Route::get('/me',[AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::patch('/edit_profile', [UserController::class, 'update']);
        Route::delete('/delete_profile', [UserController::class, 'destroy']);

        Route::apiResource('sections', SectionController::class)->middleware('role:owner,employed')->only('store', 'update', 'destroy');
        Route::apiResource('categories', CategoryController::class)->middleware('role:owner,employed')->only('store', 'update', 'destroy');
        Route::apiResource('products', ProductController::class)->middleware('role:owner,employed')->only('store', 'update', 'destroy');
        Route::apiResource('stocks', StockController::class)->middleware('role:owner,employed')->only('store', 'update', 'destroy');
        Route::apiResource('images', ImageController::class)->middleware('role:owner,employed')->only('store', 'update', 'destroy');

        Route::apiResource('orders', OrderController::class)->only('store', 'show', 'index', 'update');
        Route::apiResource('products.califications', CalificationController::class)->only('store', 'update', 'destroy');
});
