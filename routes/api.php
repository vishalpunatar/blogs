<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ActivityLogController;
Use App\Http\Controllers\SuperadminController;

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

// Route::middleware('auth:passport')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Public Routes
Route::post('/signup', [AuthController::class, 'store']);
Route::post('/login',[AuthController::class,'login']);
Route::get('/blogs',[BlogController::class,'blogs']);
Route::get('/blogs/{blog}',[BlogController::class,'show']);
Route::post('/forget-password',[AuthController::class,'forgetPassword']);
Route::post('/reset-password/{token}',[AuthController::class,'resetPassword']);

// Common Routes
Route::prefix('auth')->middleware(['auth:api','UserManage'])->group(function () {
    Route::get('/show', [AuthController::class, 'profileShow']);
    Route::patch('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/activity',[ActivityLogController::class,'showActivity']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

// Common Routes Of BlogController 
Route::prefix('blogs/{blog}/')->middleware(['auth:api','UserManage'])->group(function () {
   
    Route::prefix('comments')->group(function () {
        Route::get('/', [BlogController::class, 'showComment']);
        Route::post('/', [BlogController::class, 'addComment']);
        Route::post('/{comment}/reply', [BlogController::class, 'addReply']);
    });
    
    Route::prefix('likes')->group(function () {
        Route::post('/', [BlogController::class, 'addLike']);
        Route::get('/', [BlogController::class, 'showLike']);
    });
});