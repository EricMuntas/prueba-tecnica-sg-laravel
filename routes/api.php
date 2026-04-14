<?php

use App\Http\Controllers\API\CategoryController;
use Illuminate\Support\Facades\Route;


Route::apiResource('categories', CategoryController::class);
