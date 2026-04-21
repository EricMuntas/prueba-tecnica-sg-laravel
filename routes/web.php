<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

Route::get('/', [PageController::class, 'dashboard']);

Route::get('/products', [PageController::class, 'productIndex']);
Route::get('/products/{id}', [PageController::class, 'productShow']);

Route::get('/cart', [PageController::class, 'cartIndex']);

// Orders (web pages - require login)
Route::middleware('auth')->group(function () {
    Route::get('/orders/{id}', [PageController::class, 'orderEdit']);
});



// ADMIN PANEL
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminPageController::class, 'index']);
    Route::get('/admin/categories', [AdminPageController::class, 'categoryIndex']);
    Route::get('/admin/categories/create', [AdminPageController::class, 'categoryCreateIndex']);
    Route::get('/admin/subcategories', [AdminPageController::class, 'subcategoryIndex']);
    Route::get('/admin/subcategories/create', [AdminPageController::class, 'categoryCreateIndex']);
    Route::get('/admin/categories/{id}', [AdminPageController::class, 'categoriesEditIndex']);
    Route::get('/admin/subcategories/{id}', [AdminPageController::class, 'subcategoriesEditIndex']);
    Route::get('/admin/products', [AdminPageController::class, 'productIndex']);
    Route::get('/admin/products/create', [AdminPageController::class, 'productCreateIndex']);
    Route::get('/admin/products/{id}', [AdminPageController::class, 'productEditIndex']);
    Route::get('/admin/products/{id}/fees', [AdminPageController::class, 'productFeesIndex']);
    Route::get('/admin/fees/{id}', [AdminPageController::class, 'feeEditIndex']);
});