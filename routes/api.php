<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\SalesOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group([
    'middleware' => ['throttle:60,1'],
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});


Route::group([
    'middleware' => ['auth:api', 'throttle:60,1'],
    'prefix' => 'sales'
], function ($router) {
    Route::get('/list', [SalesOrderController::class, 'list']);
    Route::post('/order', [SalesOrderController::class, 'create']);
});

Route::group([
    'middleware' => ['auth:api', 'throttle:60,1'],
    'prefix' => 'refund'
], function ($router) {
    Route::get('/list', [RefundController::class, 'list']);
    Route::post('/add', [RefundController::class, 'create']);
});
