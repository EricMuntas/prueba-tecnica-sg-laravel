<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'dashboard']);

Route::get('/products', [PageController::class, 'productIndex']);


Route::get('/products/{id}', [PageController::class, 'productShow']);


// ADMIN PANEL, add middleware
Route::get('/admin', [AdminPageController::class, 'index']);
Route::get('/admin/categories', [AdminPageController::class, 'categoryIndex']);
Route::get('/admin/categories/create', [AdminPageController::class, 'categoryCreateIndex']);

Route::get('/admin/subcategories', [AdminPageController::class, 'subcategoryIndex']);
Route::get('/admin/subcategories/create', [AdminPageController::class, 'categoryCreateIndex']); // apuntar al mismo sitio

Route::get('/admin/categories/{id}', [AdminPageController::class, 'categoriesEditIndex']);

Route::get('/admin/products', [AdminPageController::class, 'productIndex']);
Route::get('/admin/products/create', [AdminPageController::class, 'productCreateIndex']);
Route::get('/admin/products/{id}', [AdminPageController::class, 'productEditIndex']);

Route::get('/admin/products/{id}/fees', [AdminPageController::class, 'productFeesIndex']);
Route::get('/admin/fees/{id}', [AdminPageController::class, 'feeEditIndex']);
