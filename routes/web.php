<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\PublisherController;
use App\Http\Middleware\SuperadminAuthenticate;
use App\Http\Middleware\PublisherAuthenticate;
use App\Http\Middleware\UserAuthenticate;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('users/{user}', function ($user) {
    return $user;
});

// Route::get('/signup',function(){
//     return view('signup');
// });

// Route::get('/signup',[AuthController::class,'index']);
// Route::post('/store', [AuthController::class, 'store']);
// Route::post('/login',[AuthController::class, 'login']);
// Route::get('/login',[AuthController::class,'loginpage']);
// Route::get('/blogs',[BlogController::class,'blogs']);
// //Route::view('/blogpage','blogpage');
// Route::view('/home','home');

// //super-admin Routes
// Route::prefix('/super-admin')->group(function () {
//     Route::view('/dashboard','super-admin.dashboard');
//     Route::get('/userlist',[SuperadminController::class,'userdetails']);
//     Route::get('/bloglist',[SuperadminController::class,'blogdetails']);
//     Route::get('/publist',[SuperadminController::class,'pubdetails']);
//     Route::post('/userupdate',[UserController::class,'edit']);
// })->middleware('IsSuperadmin');

// //publisher Routes
// Route::prefix('/publisher')->group(function () {
//     Route::view('/dashboard','publisher.dashboard');
//     Route::view('add-blog','publisher.add-blog');
//     Route::post('/add-blog',[BlogController::class,'add_blog']);
//     Route::get('/edit-blog/{id}',[BlogController::class,'editblog']);
//     Route::get('/myblogs',[PublisherController::class,'myblogs']);
//     Route::delete('/delete-blog/{id}',[BlogController::class,'delete']);
//     Route::get('/blogdata/{id}',[PublisherController::class,'blogdata']);
// })->middleware('IsPublisher');

// //user Routes
// Route::prefix('/user')->group(function () {
//     Route::view('/dashboard','user.dashboard');
//     Route::get('/blog',[UserController::class,'blog']);
//     Route::get('/show',[UserController::class,'show']);
//     Route::get('/edit',[UserController::class,'edit']);
//     Route::post('/update/{id}',[UserController::class,'update']);
//     Route::get('/pub-req',[UserController::class,'pub_req']);
//     Route::post('/req-stored',[UserController::class,'pub_req_save']);
    
// })->middleware('IsUser');