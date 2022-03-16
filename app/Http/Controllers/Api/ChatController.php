<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;

class ChatController extends Controller
{
    public function getChats(User $user)
    {
        $messages = array_unique(
            array_column(
                Message::where('sender', $user->id)
                    ->get()
                    ->toArray(),
                'recipient'
            )
        );
        $chats = User::whereIn('id', $messages)->get();
        return response()->json($chats);
    }
}
