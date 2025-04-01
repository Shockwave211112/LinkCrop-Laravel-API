<?php

use App\Modules\Links\Http\Controllers\GroupController;
use App\Modules\Links\Http\Controllers\LinkController;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/l/{referral}', [LinkController::class, 'referral'])->name('link.referral');

Route::group(
    ['middleware' => 'auth:sanctum'],
    function () {
        Route::group(
            ['prefix' => 'links'],
            function () {
                Route::get('/', [LinkController::class, 'index']);
                Route::post('/', [LinkController::class, 'store']);
                Route::get('/{id}', [LinkController::class, 'show']);
                Route::put('/{id}', [LinkController::class, 'put']);
                Route::patch('/{id}', [LinkController::class, 'patch']);
                Route::delete('/{id}', [LinkController::class, 'delete']);
            }
        );
        Route::group(
            ['prefix' => 'groups'],
            function () {
                Route::get('/', [GroupController::class, 'index']);
                Route::get('/all', [GroupController::class, 'all']);
                Route::post('/', [GroupController::class, 'store']);
                Route::get('/{id}', [GroupController::class, 'show']);
                Route::put('/{id}', [GroupController::class, 'put']);
                Route::patch('/{id}', [GroupController::class, 'patch']);
                Route::delete('/{id}', [GroupController::class, 'delete']);
            }
        );
        Route::group(
            ['prefix' => 'admin', 'middleware' => 'role:' . User::ADMIN],
            function () {
                Route::get('/links', [LinkController::class, 'adminIndex']);
                Route::get('/groups', [GroupController::class, 'adminIndex']);
            }
        );
    }
);
