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
<<<<<<< HEAD
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


=======
Route::get('/blog',[BlogController::class,'blog']);
Route::get('/blogdetail/{blog}',[BlogController::class,'blogData']);
Route::post('/forgetpassword',[AuthController::class,'forgetPassword']);
Route::post('/reset-password/{token}',[AuthController::class,'resetPassword']);

//super-admin Routes
Route::middleware(['auth:api','IsSuperadmin','UserManage'])->prefix('/super-admin')->group(function () {    
    Route::get('/show',[AuthController::class,'profileshow']);
    Route::get('/userlist/{name?}',[SuperadminController::class,'userList']);
    Route::get('/bloglist/{title?}',[SuperadminController::class,'blogList']);
    Route::get('/publisherlist/{name?}',[SuperadminController::class,'publisherList']);
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
    Route::get('/myblog/{title?}',[PublisherController::class,'myBlog']);
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
>>>>>>> 10b77ea (Update Routes)

