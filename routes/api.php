<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FrontStoreController;


// Define API routes for Product resource
Route::apiResource('products', ProductController::class);

// Define the login route
Route::post('/login', [AuthController::class, 'login']);

// Define the register route (add this line)
Route::post('/register', [AuthController::class, 'register']);

// Define the frontstore and cart routes
Route::get('/products', [FrontStoreController::class, 'listProducts']);
Route::post('/cart/add', [FrontStoreController::class, 'addToCart']);
Route::get('/cart/view', [FrontStoreController::class, 'viewCart']);
Route::put('/cart/update', [FrontStoreController::class, 'updateCart']);
Route::delete('/cart/remove', [FrontStoreController::class, 'removeFromCart']);
Route::post('/checkout', [FrontStoreController::class, 'checkout']);