<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\SessionsController;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', ['as' => 'login', LoginController::class, 'login'])->name('login');
Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->name('password.forgot');
Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('password.forgot');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', function () {
        return auth()->user();
    });

    Route::group([
        'prefix' => 'sessions',
        'as' => 'sessions.'
    ], function () {
        Route::get('/', [SessionsController::class, 'index']);
        Route::get('/current', [SessionsController::class, 'current']);
        Route::delete('/{tokenId}', [SessionsController::class, 'destroy']);
    });

    Route::group([
        'prefix' => 'user',
        'as' => 'user.'
    ], function () {
        Route::put('/', [UserController::class, 'update']);
        Route::post('/avatar', [UserController::class, 'setAvatar']);
        Route::post('/online', [UserController::class, 'setOnlineStatus']);
    });

    Route::group([
        'prefix' => 'chats',
        'as' => 'chats.'
    ], function () {
        Route::get('/', [ChatController::class, 'index']);
        Route::get('/{id}/messages', [MessageController::class, 'index']);
        Route::post('/messages', [MessageController::class, 'store']);
        Route::post('/{chat}/mark-as-read', [MessageController::class, 'markAsRead']);
        Route::get('/{chat}', [ChatController::class, 'show']);
    });

    Route::group([
        'prefix' => 'messages',
        'as' => 'messages.'
    ], function () {
        Route::delete('/{message}', [MessageController::class, 'destroy']);
        Route::post('/{message}', [MessageController::class, 'update']);
    });

    Route::group([
        'prefix' => 'attachments',
        'as' => 'attachments.'
    ], function () {
        Route::get('/{attachment}/download', [AttachmentController::class, 'download']);
        Route::delete('/{attachment}', [AttachmentController::class, 'destroy']);
        Route::get('/{attachment}/stream', [AttachmentController::class, 'stream']);
    });
});
