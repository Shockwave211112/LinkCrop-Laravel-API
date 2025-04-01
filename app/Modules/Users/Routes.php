<?php

use App\Modules\Users\Http\Controllers\UserController;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::group(['prefix' => 'user'],
        function () {
            Route::get('/info', [UserController::class, 'getInfo']);
            Route::group(
                ['middleware' => 'role:' . User::ADMIN],
                function () {
                    Route::get('/', [UserController::class, 'index']);
                    Route::post('/', [UserController::class, 'store']);
                    Route::get('/{id}', [UserController::class, 'show']);
                    Route::put('/{id}', [UserController::class, 'put']);
                    Route::patch('/{id}', [UserController::class, 'patch']);
                    Route::delete('/{id}', [UserController::class, 'delete']);
                });
        }
    );
});
