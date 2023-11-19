<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(UserController::class)->prefix('user')->as('user.')->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
});

Route::controller(ProductController::class)->prefix('product')->as('product.')->middleware('jwt')->group(function () {
    Route::get('show', 'show');
    Route::post('create', 'create');
    Route::put('update/{productId}', 'update');
    Route::delete('delete/{productId}', 'delete');
});

