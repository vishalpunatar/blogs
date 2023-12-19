<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\BlogController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\SuperadminAuthenticate;
use App\Http\Middleware\PublisherAuthenticate;
use App\Http\Middleware\UserAuthenticate;
use App\Http\Middleware\UserManage;


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

Route::post('/signup', [AuthController::class, 'store']);
Route::post('/login',[AuthController::class,'login']);
Route::get('/blog',[BlogController::class,'blog']);
Route::get('/blog-detail/{blog}',[BlogController::class,'blogData']);
Route::post('/forget-password',[AuthController::class,'forgetPassword']);
Route::post('/reset-password/{token}',[AuthController::class,'resetPassword']);

//super-admin Routes
Route::middleware(['auth:api','IsSuperadmin','UserManage'])->prefix('/super-admin')->group(function () {    
    Route::get('/show',[AuthController::class,'profileshow']);
    Route::get('/user-list/{name?}/{email?}',[SuperadminController::class,'userList']);
    Route::get('/blog-list/{title?}',[SuperadminController::class,'blogList']);
    Route::get('/publisher-list/{name?}/{email?}',[SuperadminController::class,'publisherList']);
    Route::get('/blog-request',[SuperadminController::class,'blogRequestList']);
    Route::post('/blog-approval/{blog}',[SuperadminController::class,'blogApproval']);
    Route::get('/publisher-request',[SuperadminController::class,'publisherRequestList']);
    Route::post('/publisher-approval/user_id/{user}',[SuperadminController::class,'publisherApproval']);
    Route::post('/edit-user/{user}',[SuperadminController::class,'editUser']);
    Route::post('/edit-blog/{blog}',[SuperadminController::class,'editBlog']);
    Route::delete('/user-delete/{user}',[SuperadminController::class,'userDelete']);
    Route::delete('/blog-delete/{blog}',[SuperadminController::class,'blogDelete']);
    Route::post('/like/{blog}',[BlogController::class,'addLike']);
    Route::post('/comment/{blog}',[BlogController::class,'addComment']);
    Route::post('/reply/{comment}',[BlogController::class,'addReply']);
    Route::get('/show-comment/{blog}',[BlogController::class,'showComment']);
    Route::get('/show-like/{blog}',[BlogController::class,'showLike']);
    Route::get('/show-reply/{blog}',[BlogController::class,'showReply']);
    Route::post('/change-password',[AuthController::class,'changePassword']);
    Route::post('/manage-user/{user}',[SuperadminController::class,'manageUser']);
    Route::get('/logout',[AuthController::class,'logout']);
});

//publisher Routes
Route::middleware(['auth:api','IsPublisher','UserManage'])->prefix('/publisher')->group(function () {
    Route::get('/show',[AuthController::class,'profileshow']);
    Route::post('/create-blog',[BlogController::class,'createBlog']);
    Route::post('/edit-blog/{blog}',[BlogController::class,'editBlog']);
    Route::get('/myblog/{title?}',[PublisherController::class,'myBlog']);
    Route::delete('/blog-delete/{blog}',[BlogController::class,'blogDelete']);
    Route::post('/like/{blog}',[BlogController::class,'addLike']);
    Route::post('/comment/{blog}',[BlogController::class,'addComment']);
    Route::post('/reply/{comment}',[BlogController::class,'addReply']);
    Route::get('/show-comment/{blog}',[BlogController::class,'showComment']);
    Route::get('/show-like/{blog}',[BlogController::class,'showLike']);
    Route::get('/show-reply/{blog}',[BlogController::class,'showReply']);
    Route::post('/change-password',[AuthController::class,'changePassword']);
    Route::get('/logout',[AuthController::class,'logout']);
});

//user Routes
Route::middleware(['auth:api','IsUser','UserManage'])->prefix('/user')->group(function () {
    Route::get('/show',[AuthController::class,'profileshow']);
    Route::post('/edit',[UserController::class,'edit']);
    Route::post('/publisher-request',[UserController::class,'publisherRequest']);
    Route::post('/comment/{blog}',[BlogController::class,'addComment']);
    Route::post('/reply/{comment}',[BlogController::class,'addReply']);
    Route::post('/like/{blog}',[BlogController::class,'addLike']);
    Route::post('/change-password',[AuthController::class,'changePassword']);
    Route::get('/logout',[AuthController::class,'logout']);
});