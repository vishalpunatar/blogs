<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\UserController;


Route::middleware(['IsPublisher','UserManage'])->prefix('publisher/')->group(function () {

    // BlogController routes
    Route::prefix('blogs/')->group(function () {
        Route::post('/create', [BlogController::class, 'createBlog']);
        Route::post('/{blog}/edit', [BlogController::class, 'editBlog']);
        Route::delete('/{blog}/delete', [BlogController::class, 'blogDelete']);
    });

    // Additional routes
    Route::get('/myblogs', [PublisherController::class, 'myBlog']);
    Route::get('/api-toggle', [UserController::class, 'apiToggle']);

});