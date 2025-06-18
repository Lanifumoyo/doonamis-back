<?php

use Doonamis\Auth\Infrastructure\Http\Controllers\AuthController;
use Doonamis\User\Infrastructure\Http\Controller\UserController;
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
    Route::prefix('users')->group(function () {
        Route::get('', [UserController::class, 'index'])->middleware('auth:api');
        Route::delete('{id}', [UserController::class, 'destroy'])->middleware('auth:api');
        Route::post('upload-from-csv', [UserController::class, 'uploadFromCsv'])->middleware('auth:api');
    });
});