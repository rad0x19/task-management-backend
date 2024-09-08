<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

// Secured Routes
Route::group([ 'middleware' => [ 'auth:sanctum', 'users' ], 'prefix' => 'authenticated' ], function(){
    // user details
    Route::get('user', function (Request $request) {
        return $request->user();
    });
    // logout route
    Route::post('logout', [ AuthController::class , 'logout' ]);
    // task routes
    Route::group([ 'prefix' => 'tasks' ], function() {
        // create
        Route::post('create', [ TaskController::class, 'create' ]);
        // list
        Route::get('list', [ TaskController::class, 'listAndSearch' ]);
        // update status
        Route::post('status/update', [ TaskController::class, 'markAsFunction' ]);
    });
});

// public routes
Route::group([ 'middleware' => 'guest' ], function() {
    // auth routes
    Route::group([ 'prefix' => 'auth' ], function() {
        // login
        Route::post('login', [ AuthController::class, 'login' ]);
        // registration
        Route::post('register', [ AuthController::class, 'register' ]);
    });
});
