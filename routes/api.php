<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Mime\MessageConverter;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', ['as' => 'login', LoginController::class, 'login'])->name('login');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', function () {
        return auth()->user();
    });
    Route::put('/user', [LoginController::class, 'updateUser']);
    Route::post('/user/avatar', [LoginController::class, 'updateAvatar']);

    Route::get('/chats', [ChatController::class, 'getChats']);
    Route::get('/chat/{id}/messages', [MessageController::class, 'index']);
    Route::post('/chats/messages', [MessageController::class, 'store']);
    Route::post('/chat/{id}/markasread', [MessageController::class, 'markAsRead']);
    Route::get('/chat/{id}', [ChatController::class, 'getChat']);
    Route::delete('/message/{message}', [MessageController::class, 'destroy']);
});
