<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UsersController;
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
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/users', [UsersController::class, 'index']);
Route::post('/users', [UsersController::class, 'store']);
Route::middleware('auth:sanctum')->get('/users/{id}', [UsersController::class, 'show']);
Route::middleware('auth:sanctum')->put('/users/{id}', [UsersController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/users/{id}', [UsersController::class, 'destroy']);

Route::get('/products', [ProductsController::class, 'index']);
Route::middleware('auth:sanctum')->post('/products', [ProductsController::class, 'store']);
Route::get('/products/{id}', [ProductsController::class, 'show']);
Route::middleware('auth:sanctum')->put('/products/{id}', [ProductsController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/products/{id}', [ProductsController::class, 'destroy']);

Route::middleware('auth:sanctum')->post('/deposit', [TransactionController::class, 'deposit']);
