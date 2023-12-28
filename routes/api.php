<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:passport')->get('/user', function (Request $requestuest) {
//     return $requestuest->user();
// });

// Public Routes
Route::post('/signup', [AuthController::class, 'store']);
Route::post('/login',[AuthController::class,'login']);
Route::get('/blogs',[BlogController::class,'blogs']);
Route::get('/blogs/{blog}',[BlogController::class,'blogData']);
Route::post('/forget-password',[AuthController::class,'forgetPassword']);
Route::post('/reset-password/{token}',[AuthController::class,'resetPassword']);

// Common Routes
Route::prefix('auth')->middleware(['auth:api','UserManage'])->group(function () {
    Route::get('/show', [AuthController::class, 'profileShow']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/activity',[ActivityLogController::class,'showActivity']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

// Common Routes Of BlogController 
Route::prefix('blogs')->middleware(['auth:api','UserManage'])->group(function () {
    Route::post('/{blog}/like', [BlogController::class, 'addLike']);
    Route::post('/{blog}/comment', [BlogController::class, 'addComment']);
    Route::post('{blog}/comments/{comment}/reply', [BlogController::class, 'addReply']);
    Route::get('{blog}/comments/show', [BlogController::class, 'showComment']);
    Route::get('/{blog}/likes/show', [BlogController::class, 'showLike']);
});       




