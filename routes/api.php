<?php

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

Route::post('/authenticate', [\App\Http\Controllers\ApiController::class, 'authenticate']);

Route::middleware('auth')->prefix('/app')->group(function () {
    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/users', [\App\Http\Controllers\ApiController::class, 'users']);
    });
});

