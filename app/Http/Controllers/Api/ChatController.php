<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;

class ChatController extends Controller
{
    public function getChats()
    {
        /** @var User $user  */
        $user = auth()->user();
        $messagesWithUser = Message::where('sender', $user->id)
            ->orWhere('recipient', $user->id)
            ->orderBy('created_at')
            ->get()
            ->toArray();
        $recived = array_column(
            $messagesWithUser,
            'recipient'
        );
        $sent = array_column(
            $messagesWithUser,
            'sender'
        );
        $messages = array_unique(array_merge($recived, $sent));
        $chats = User::whereIn('id', $messages)->where('id', "!=", $user->id)->get();
        return response()->json($chats);
    }
}
