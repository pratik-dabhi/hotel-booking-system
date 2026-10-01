<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\AvailabilityController;
use App\Http\Controllers\API\V1\BookingController;
use App\Http\Controllers\API\V1\HotelController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/availability/hotels', [AvailabilityController::class, 'index']);

    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::group(['prefix' => 'hotel'], function () {
            Route::get('/', [HotelController::class, 'index']);
            Route::post('/', [HotelController::class, 'store']);
            Route::get('/{id}', [HotelController::class, 'getById']);
            Route::patch('/{id}', [HotelController::class, 'update']);
            Route::delete('/{id}', [HotelController::class, 'destroy']);
        });

        Route::group(['prefix' => 'bookings'], function () {
            Route::get('/', [BookingController::class, 'index']);
            Route::post('/', [BookingController::class, 'store']);
            Route::patch('//{id}/cancel', [BookingController::class, 'cancel']);
        });
    });
});
