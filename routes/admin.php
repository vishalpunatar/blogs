<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Blog;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\UserController;

Route::prefix('super-admin/')->middleware(['IsSuperadmin'])->group(function () {
    
    // Additional route
    Route::get('/users', [SuperadminController::class, 'users']);
    Route::get('/blogs', [SuperadminController::class, 'blogs']);
    Route::get('/user-requests', [SuperadminController::class, 'userRequests']);
    Route::patch('/user-requests/{user}/approved', [SuperadminController::class, 'userApproval']);
    Route::get('/blogs/requests', [SuperadminController::class, 'blogRequests']);
    Route::get('/publishers', [SuperadminController::class, 'publishers']);
    Route::get('/activity', [ActivityLogController::class, 'allActivity']);

    // Superadmin routes
    Route::prefix('blogs/{blog}/')->group(function () {
        Route::get('/',[BlogController::class,'show']);
        Route::patch('/', [SuperadminController::class, 'editBlog']);
        Route::patch('/approved', [SuperadminController::class, 'blogApproval']);
        Route::delete('/', [SuperadminController::class, 'blogDelete']);
        Route::delete('/comments/{comment}', [SuperadminController::class, 'deleteComment']);
    });
    
    Route::prefix('users/{user}/')->group(function () {
        Route::get('/',[SuperadminController::class,'userShow']);
        Route::patch('/', [SuperadminController::class, 'editUser']);
        Route::delete('/', [SuperadminController::class, 'userDelete']);
        Route::patch('/manage', [SuperadminController::class, 'manageUser']);
    });
    
});
