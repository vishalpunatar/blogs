<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::middleware(['IsUser','UserManage'])->prefix('user/')->group(function () {
    
    // UserController routes
    Route::post('/edit', [UserController::class, 'edit']);
    Route::post('/publisher-request', [UserController::class, 'publisherRequest']);
    Route::get('/api-toggle', [UserController::class, 'apiToggle']);
});

   