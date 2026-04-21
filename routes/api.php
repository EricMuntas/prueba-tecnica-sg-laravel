<?php

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\FeeController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SubcategoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('categories', CategoryController::class);
Route::apiResource('subcategories', SubcategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('fees', FeeController::class);

// Orders require a logged-in session
Route::middleware('auth:web')->apiResource('orders', OrderController::class);
