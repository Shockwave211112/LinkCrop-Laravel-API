<?php

use App\Modules\Auth\Http\Controllers\AuthController;
use App\Modules\Auth\Http\Controllers\PermissionsController;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'],
    function () {
        Route::post('/login', [AuthController::class, 'login']);

        Route::get('/{provider}/redirect', [AuthController::class, 'oauth']);
        Route::get('/{provider}/callback', [AuthController::class, 'callback']);

        Route::post('/registration', [AuthController::class, 'registration']);

        Route::get('/reset-password', [AuthController::class, 'getResetPassword'])->name('password.reset');
        Route::post('/change-password', [AuthController::class, 'resetPassword']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('/logout', [AuthController::class, 'logout']);
        });
    });

Route::get('/email/verify', [AuthController::class, 'emailVerify'])->name('email.verify');

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('/email/resend', [AuthController::class, 'resend']);

    Route::group(['prefix' => 'permissions', 'middleware' => 'role:' . User::ADMIN], function() {
        Route::get('/', [PermissionsController::class, 'index']);
        Route::get('/{id}', [PermissionsController::class, 'show']);
        Route::post('/sync', [PermissionsController::class, 'sync']);
    });
});
