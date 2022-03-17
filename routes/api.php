<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', ['as' => 'login', LoginController::class, 'login'])->name('login');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', function () {
        return auth()->user();
    });
    Route::get('/chats', [ChatController::class, 'getChats']);
    Route::get('/chat/{id}/messages', [MessageController::class, 'index']);
    Route::post('/chats/messages', [MessageController::class, 'store']);
});
