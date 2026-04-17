<?php

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\FeeController;
use App\Http\Controllers\API\OrderController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SubcategoryController;

Route::apiResource('categories', CategoryController::class);
Route::apiResource('subcategories', SubcategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('orders', OrderController::class);
Route::apiResource('fees', FeeController::class);
