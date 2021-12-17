<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UsersController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::post('/signup', [UsersController::class, 'signup']);
Route::post('/login', [UsersController::class, 'login']);
Route::get('/products', [ProductsController::class, 'index']);
Route::get('/products/{id}', [ProductsController::class, 'show']);
Route::get('/category', [CategoryController::class, 'index']);
Route::post('/search', [ProductsController::class, 'search']);

Route::get('/images/{id}', [ImagesController::class, 'getImage']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/products', [ProductsController::class, 'store']);
    Route::post('/category', [CategoryController::class, 'store']);
    Route::delete('/products/{id}', [ProductsController::class, 'destroy']);
    Route::post('/logout', [UsersController::class, 'logout']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
