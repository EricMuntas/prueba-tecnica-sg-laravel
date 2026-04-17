<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'dashboard']);

Route::get('/products', [PageController::class, 'product_index']);


Route::get('/products/{id}', [PageController::class, 'product_show']);


// ADMIN PANEL, add middleware
Route::get('/admin', [PageController::class, 'admin_panel']);
Route::get('/admin/categories', [PageController::class, 'category_index']);
Route::get('/admin/categories-create', [PageController::class, 'category_create_index']);
Route::get('/admin/subcategories', [PageController::class, 'subcategory_index']);
