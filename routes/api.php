<?php

use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Endpoints principales requeridos para productos
Route::apiResource('products', ProductController::class);

// Endpoints requeridos para precios de productos en diferentes divisas
Route::get('products/{id}/prices', [ProductPriceController::class, 'index']);
Route::post('products/{id}/prices', [ProductPriceController::class, 'store']);

// Gestión de divisas
Route::apiResource('currencies', CurrencyController::class)->only(['index', 'store', 'show']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

