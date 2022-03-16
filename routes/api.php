<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', ['as' => 'login', LoginController::class, 'login'])->name('login');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', function () {
        return auth()->user();
    });
});
Route::get('/chats/{user}', [ChatController::class, 'getChats']);
