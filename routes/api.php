<?php

use Doonamis\Auth\Infrastructure\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1'
], function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });
});