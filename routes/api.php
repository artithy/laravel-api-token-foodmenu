<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CuisineController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\AuthMiddleWare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/test', [UserController::class, 'test']);


Route::middleware([AuthMiddleWare::class])->group(function () {
    Route::get('/cuisin', [CuisineController::class, 'getAllCuisine']);
    Route::post('/cuisin', [CuisineController::class, 'store']);
    Route::post('/food', [FoodController::class, 'store']);
    Route::get('/food', [FoodController::class, 'foodWithCuisine']);
    Route::get('/dashboard', [UserController::class, 'dashboard']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/food/{id}', [FoodController::class, 'update']);
    Route::delete('/food/{id}', [FoodController::class, 'destroy']);
    Route::patch('/food/{id}/toggle-status', [FoodController::class, 'toggleStatus']);
});

Route::get('/foods/active', [FoodController::class, 'activeFoods']);
Route::post('/cart/add', [CartController::class, 'add']);
Route::get('/cart/{cart_token}', [CartController::class, 'get']);
Route::post('/order', [OrderController::class, 'placeOrder']);
Route::get('/orders', [OrderController::class, 'getAll']);
Route::post('/cart/item', [CartItemController::class, 'addItem']);
Route::get('cuisines-with-food', [CuisineController::class, 'getCuisineWithFood']);


Route::post('/cart/guest/add', [CartController::class, 'addGuestItem']);
Route::get('/cart/guest/{cart_token}', [CartController::class, 'getGuestCart']);
