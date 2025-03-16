<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JWTAuthController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\ItemController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('register', [JWTAuthController::class, 'register']);
Route::post('login', [JWTAuthController::class, 'login']);

// Protected routes
Route::middleware('jwt.auth')->group(function () {
    // Auth routes
    Route::get('user', [JWTAuthController::class, 'getUser']);
    Route::post('logout', [JWTAuthController::class, 'logout']);
    
    // Product Type routes
    Route::apiResource('product-types', ProductTypeController::class);
    
    // Item routes
    Route::get('items', [ItemController::class, 'index']);
    Route::post('items', [ItemController::class, 'store']);
    Route::post('items/batch', [ItemController::class, 'storeBatch']);
    Route::get('items/{item}', [ItemController::class, 'show']);
    Route::put('items/{item}', [ItemController::class, 'update']);
    Route::patch('items/{item}/toggle-sold', [ItemController::class, 'toggleSold']);
    Route::delete('items/{item}', [ItemController::class, 'destroy']);
}); 