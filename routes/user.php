<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Models\User;


Route::middleware(['IsUser','UserManage'])->prefix('user')->group(function () {
    
    // UserController routes
    Route::patch('/', [UserController::class, 'edit']);
    Route::post('/request', [UserController::class, 'sendRequest']);
    Route::patch('/api-toggle/{status}', [UserController::class, 'apiToggle']);
});   