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
Route::post('/blog',[BlogController::class,'blog']);
Route::get('/blogdetail/{blog}',[BlogController::class,'blogData']);
Route::post('/forgetpassword',[AuthController::class,'forgetPassword']);
Route::post('/reset-password/{token}',[AuthController::class,'resetPassword']);

//super-admin Routes
Route::middleware(['auth:api','IsSuperadmin','UserManage'])->prefix('/super-admin')->group(function () {    
    Route::get('/show',[AuthController::class,'profileshow']);
    Route::post('/userlist',[SuperadminController::class,'userList']);
    Route::post('/bloglist',[SuperadminController::class,'blogList']);
    Route::post('/publisherlist',[SuperadminController::class,'publisherList']);
    Route::get('/blog/request',[SuperadminController::class,'blogRequestList']);
    Route::post('/blog/approval/{blog}',[SuperadminController::class,'blogApproval']);
    Route::get('/publisher/request',[SuperadminController::class,'publisherRequestList']);
    Route::post('/publisher/approval/id/{id}/token/{token}',[SuperadminController::class,'publisherApproval']);
    Route::post('/edituser/{user}',[SuperadminController::class,'editUser']);
    Route::post('/editblog/{blog}',[SuperadminController::class,'editBlog']);
    Route::delete('/userdelete/{user}',[SuperadminController::class,'userDelete']);
    Route::delete('/blogdelete/{blog}',[SuperadminController::class,'blogDelete']);
    Route::post('/addlike/{blog}',[BlogController::class,'addLike']);
    Route::post('/addcomment/{blog}',[BlogController::class,'addComment']);
    Route::post('/addreply/{comment}',[BlogController::class,'addReply']);
    Route::get('/showcomment/{blog}',[BlogController::class,'showComment']);
    Route::get('/showlike/{blog}',[BlogController::class,'showLike']);
    Route::get('/showreply/{blog}',[BlogController::class,'showReply']);
    Route::post('/change/password',[AuthController::class,'changePassword']);
    Route::post('/manage/user/{user}',[SuperadminController::class,'manageUser']);
    Route::get('/logout',[AuthController::class,'logout']);
    //Route::get('/fetchblog/{blog}',[SuperadminController::class,'fetchBlogForEdit']);
});

//publisher Routes
Route::middleware(['auth:api','IsPublisher','UserManage'])->prefix('/publisher')->group(function () {
    Route::get('/show',[AuthController::class,'profileshow']);
    Route::post('/createblog',[BlogController::class,'createBlog']);
    Route::post('/editblog/{blog}',[BlogController::class,'editBlog']);
    Route::get('/myblog',[PublisherController::class,'myBlog']);
    Route::delete('/blogdelete/{blog}',[BlogController::class,'blogDelete']);
    Route::post('/addlike/{blog}',[BlogController::class,'addLike']);
    Route::post('/addcomment/{blog}',[BlogController::class,'addComment']);
    Route::post('/addreply/{comment}',[BlogController::class,'addReply']);
    Route::get('/showcomment/{blog}',[BlogController::class,'showComment']);
    Route::get('/showlike/{blog}',[BlogController::class,'showLike']);
    Route::get('/showreply/{blog}',[BlogController::class,'showReply']);
    Route::post('/change/password',[AuthController::class,'changePassword']);
    Route::get('/logout',[AuthController::class,'logout']);
    //Route::get('/fetchblog/{blog}',[SuperadminController::class,'fetchBlogForEdit']);
});

//user Routes
Route::middleware(['auth:api','IsUser','UserManage'])->prefix('/user')->group(function () {
    Route::get('/show',[AuthController::class,'profileshow']);
    Route::post('/edit',[UserController::class,'edit']);
    Route::post('/publisher/request',[UserController::class,'publisherRequest']);
    Route::post('/addcomment/{blog}',[BlogController::class,'addComment']);
    Route::post('/addreply/{comment}',[BlogController::class,'addReply']);
    Route::post('/addlike/{blog}',[BlogController::class,'addLike']);
    Route::post('/change/password',[AuthController::class,'changePassword']);
    Route::get('/logout',[AuthController::class,'logout']);
});