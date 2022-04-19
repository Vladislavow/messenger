<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use App\Models\Attachment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', ['as' => 'login', LoginController::class, 'login'])->name('login');
Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->name('password.forgot');
Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('password.forgot');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', function () {
        return auth()->user();
    });
    Route::get('/sessions', function () {
        return auth()->user()->tokens;
    });
    Route::get('/current_token_id', function () {
        return auth()->user()->currentAccessToken()->id;
    });
    Route::delete('/session/{id}', function ($id) {
        auth()->user()->tokens()->where('id', $id)->first()->delete();
        return response()->json('Session closed');
    });
    Route::put('/user', [UserController::class, 'updateUser']);
    Route::post('/user/avatar', [UserController::class, 'updateAvatar']);
    Route::post('/user/online', [UserController::class, 'setOnlineStatus']);

    Route::get('/chats', [ChatController::class, 'getChats']);
    Route::get('/chat/{id}/messages', [MessageController::class, 'index']);
    Route::post('/chats/messages', [MessageController::class, 'store']);
    Route::post('/chat/{id}/markasread', [MessageController::class, 'markAsRead']);
    Route::get('/chat/{id}', [ChatController::class, 'getChat']);
    Route::delete('/message/{message}', [MessageController::class, 'destroy']);
    Route::get('/download/{attachment}', [MessageController::class, 'downloadAttachment']);
    Route::post('/message/{message}', [MessageController::class, 'update']);
    Route::delete('/attachment/{attachment}', [MessageController::class, 'deleteAttachment']);
    Route::get('/attachment/{attachment}', [MessageController::class, 'getAttachment']);
});
