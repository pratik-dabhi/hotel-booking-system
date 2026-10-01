<?php

use App\Http\Controllers\API\V1\AuthController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'v1'], function(){
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});