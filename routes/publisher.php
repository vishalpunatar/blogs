<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\UserController;


Route::middleware(['IsPublisher','UserManage'])->prefix('publisher/')->group(function () {

    // Publisher routes
    Route::prefix('blogs')->group(function () {
        Route::post('/', [BlogController::class, 'store']);
        Route::get('/', [PublisherController::class, 'myBlog']);
        Route::patch('/{blog}', [PublisherController::class, 'editBlog']);
        Route::delete('/{blog}', [PublisherController::class, 'blogDelete']);
        Route::delete('/{blog}/comments/{comment}',[PublisherController::class,'commentDelete']);
    });

    // Additional routes
    Route::get('/api-toggle/{status}', [UserController::class, 'apiToggle']);
});