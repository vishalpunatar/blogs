<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ActivityLogController;

Route::middleware(['IsSuperadmin','UserManage'])->prefix('super-admin/')->group(function () {

    // SuperadminController routes
    Route::get('/users', [SuperadminController::class, 'userList']);
    Route::get('/blogs', [SuperadminController::class, 'blogList']);
    Route::get('/publishers', [SuperadminController::class, 'publisherList']);
    Route::get('/blogs/requests', [SuperadminController::class, 'blogRequestList']);
    Route::post('/blogs/{blog}/approval', [SuperadminController::class, 'blogApproval']);
    Route::post('/blogs/{blog}/edit', [SuperadminController::class, 'editBlog']);
    Route::delete('/blogs/{blog}/delete', [SuperadminController::class, 'blogDelete']);
    Route::post('/users/{user}/edit', [SuperadminController::class, 'editUser']);
    Route::delete('/users/{user}/delete', [SuperadminController::class, 'userDelete']);
    Route::post('/users/{user}/manage', [SuperadminController::class, 'manageUser']);
    Route::get('/publishers/requests', [SuperadminController::class, 'publisherRequestList']);
    Route::post('/publishers/{user}/approval', [SuperadminController::class, 'publisherApproval']);
 
    // Additional route
    Route::get('/activity', [ActivityLogController::class, 'allActivity']);
    Route::delete('/blogs/{blog}/comments/{comment}/delete', [BlogController::class, 'deleteComment']);

});

