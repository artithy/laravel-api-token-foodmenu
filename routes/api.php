<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CuisineController;
use App\Http\Controllers\FoodController;
use App\Http\Middleware\AuthMiddleWare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);


Route::middleware([AuthMiddleWare::class])->group(function () {
    Route::get('/cuisin', [CuisineController::class, 'getAllCuisine']);
    Route::post('/cuisin', [CuisineController::class, 'store']);
    Route::post('/food', [FoodController::class, 'store']);
    Route::get('/food', [FoodController::class, 'foodWithCuisine']);
    Route::get('/dashboard', [UserController::class, 'dashboard']);
    Route::post('/logout', [UserController::class, 'logout']);
});
